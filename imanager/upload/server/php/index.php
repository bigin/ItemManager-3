<?php
// bootstrapping ItemManager core
$root = dirname(dirname(dirname(dirname(__DIR__))));
include_once($root.'/imanager.php');
//error_reporting(E_ALL | E_STRICT);
//require_once($root.'/gsconfig.php');
//$gsadmindir = (defined('GSADMIN') ? GSADMIN : 'admin');
//require_once($root.'/'.$gsadmindir.'/inc/common.php');


//if(!get_cookie('GS_ADMIN_USERNAME')) {die();}
require('UploadHandler.php');
//$upload_handler = new \Imanager\UploadHandler(null, false);
$upload_handler = new \Imanager\UploadHandler();
