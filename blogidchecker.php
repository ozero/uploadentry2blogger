<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
require_once 'SimpleCRUD.class.php';
require_once 'Spyc.class.php';

$settings = Spyc::YAMLLoad('settings.yaml');
//
if (($settings['user'] == null) || ($settings['pass'] == null)) {
    exit("configure setting.yaml to set your blogger id and password, first.\n");
}

$_obj_sc = new SimpleCRUD($settings['user'], $settings['pass']);
$_obj_sc->promptForBlogID();




function getInput($text)
{
    echo $text.': ';
    return trim(fgets(STDIN));
}
