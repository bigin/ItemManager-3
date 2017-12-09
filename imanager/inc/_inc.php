<?php

if(!isset($_SESSION)){session_start();}
if(!isset($_SESSION['cat']) || is_null($_SESSION['cat'])) $_SESSION['cat'] = null;

/*register_plugin(
	$thisfile,
	'ItemManager',
	IM_VERSION_GS,
	'Juri Ehret',
	'http://ehret-studio.com',
	'A simple flat-file framework for GetSimple-CMS',
	'imanager',
	'im_render_backend'
);*/

// activate actions
//add_action('admin-pre-header', 'ajaxGetLists');
//add_action('nav-tab', 'createNavTab', array($thisfile, $thisfile, 'Manager'));
//register_style('jqui', IM_SITE_URL.'plugins/'.$thisfile.'/upload/js/jquery-ui/jquery-ui.css',
//	GSVERSION, 'screen');
//register_style('imstyle', IM_SITE_URL.'plugins/'.$thisfile.'/css/im-styles.css',
//	GSVERSION, 'screen');
//register_style('blueimp',  IM_SITE_URL.'plugins/'.$thisfile.'/css/blueimp-gallery.min.css',
//	GSVERSION, 'screen');
//register_style('imstylefonts', IM_SITE_URL.'plugins/'.$thisfile
//	.'/css/fonts/font-awesome/css/font-awesome.min.css', GSVERSION, 'screen');
//queue_style('jqui', GSBACK);
//queue_style('imstyle', GSBACK);
//queue_style('imstylefonts', GSBACK);
//queue_style('blueimp', GSBACK);


// Define constants
include_once(__DIR__.'/_def.php');

// Util
include_once(IM_SOURCEPATH.'_Util.php');
// Manager
include_once(IM_SOURCEPATH.'Manager.php');
// ItemManager
include_once(IM_SOURCEPATH.'ItemManager.php');

/**
 * Core ItemManager's function, we use it to create an ItemManager instance
 *
 * @param string $name
 *
 * @return Im\ItemManager instance
 */
function imanager($name='')
{
	global $im;
	if($im === null) $im = new Imanager\ItemManager();
	return !empty($name) ? $im->$name : $im;
}
