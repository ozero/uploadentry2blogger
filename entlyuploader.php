<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
require_once 'SimpleCRUD.class.php';
require_once 'Spyc.class.php';

//init
$baseset=Spyc::YAMLLoad('settings.yaml');
$settings = Spyc::YAMLLoad($baseset['blogacc']);
if (($settings['user'] == null) || ($settings['pass'] == null)) {
    exit("configure setting.yaml to set your blogger id and password, first.\n");
}
if ($settings['blogid'] == null) {
    exit("run blodidchecker.php on your shell, and configure blogid on setting yaml.\n");
}
if ($settings['maxuploadperday'] == null) {
    exit("configure maxuploadperday on setting yaml. or you'll face API limitation (50 new posts/day).\n");
}
$_obj_sc = new SimpleCRUD($settings['user'], $settings['pass']);
$_obj_sc->blogID = $settings['blogid'];

//upload
$files = glob($baseset['htmldir'].'/*.html');
foreach($files as $k0=>$v0){
	if($k0 > $settings['maxuploadperday']){continue;}
	print "upload: {$v0}\n";
	$postID = upload($_obj_sc,$v0);
	print "done.\n";
	rename($v0,$v0.".uploaded_".$postID);
}



function upload(&$blogobj,$path){
	//load
	$htmlcontent=join('',file($path));
	
	//extract metadata
	//<!-- title:my memorial entry -->
	preg_match("/<!\-\-.title:(.*?).\-\->/",$htmlcontent,$matches);
	$entrytitle=$matches[1];
	//<!-- date:2001-01-1T09:00:00+09:00 -->
	preg_match("/<!\-\-.date:(.*?).\-\->/",$htmlcontent,$matches);
	$entrydate=$matches[1];
	
	//create post
	$arg=array(
	    'contenttype'=>'html',
	    'entrytitle'=>$entrytitle,
	    'entrycontent'=>$htmlcontent,
	    'is_draft'=>true
	);
	//print_r($arg);
	$postID = $blogobj->createPost($arg);
	
	//add date to the post
	$arg=array(
		'post_id'=>$postID,
	    'date_published'=>$entrydate,
	    'date_updated'=>$entrydate,
	    'is_draft'=>false
	);
	//print_r($arg);
	$updatedPost = $blogobj->updatePost($arg);
	return $postID;
}
