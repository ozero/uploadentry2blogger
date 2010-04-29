<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
require_once 'SimpleCRUD.class.php';
require_once 'Spyc.class.php';

//init
$settings = Spyc::YAMLLoad('settings.yaml');
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

exit;


$files = glob("./html/*.html");
foreach($files as $k0=>$v0){
	if($k0 > $settings['maxuploadperday']){continue;}
	print "upload: {$v0}\n";
	
	//load
	$htmlcontent=join('',file($v0));
	
	//extract metadata
	//<!-- title:my memorial entry -->
	preg_match("/<!\-\-.title:(.*?).\-\->/","",$htmlcontent,$matches);
	$entrytitle=$matches[1];
	//<!-- date:2001-01-1T09:00:00+09:00 -->
	preg_match("/<!\-\-.date:(.*?).\-\->/","",$htmlcontent,$matches);
	$entrydate=$matches[1];
	
	//create post
	$arg=array(
	    'contenttype'=>'html',
	    'entrytitle'=>$entrytitle,
	    'entrycontent'=>join("\n",$htmlcontent),
	    'is_draft'=>true
	);
	$postID = $_obj_sc->createPost($arg);
	
	//add date to the post
	$entrydate=$matches[1]."T09:0".substr(microtime(),3,1).":00+09:00";
	$arg=array(
		'post_id'=>$postID,
	    'date_published'=>$entrydate,
	    'date_updated'=>$entrydate,
	    'is_draft'=>false
	);
	$updatedPost = $_obj_sc->updatePost($arg);
	print "done.\n";
}


