<?php

define('IS_IM', true);
define('IM_ROOTPATH', $basedir.'/');
define('IM_SOURCEPATH', IM_ROOTPATH.'imanager/lib/');
define('IM_DATAPATH', IM_ROOTPATH.'data/'); // contents
define('IM_CATEGORYPATH', IM_DATAPATH.'categories/');
define('IM_FIELDSPATH', IM_DATAPATH.'fields/');
define('IM_ITEMPATH', IM_DATAPATH.'items/');
define('IM_SETTINGSPATH', IM_DATAPATH.'settings/');
define('IM_DATASETPATH', IM_DATAPATH.'datasets/');
define('IM_CACHEPATH', IM_DATAPATH.'cache/');
define('IM_BUFFERPATH', IM_DATASETPATH.'buffers/');
define('IM_SECTIONSCACHEPATH', IM_DATASETPATH.'sections/');
define('IM_UPLOADPATH', IM_DATAPATH.'uploads/');
define('IM_TEMPLATEPATH', IM_ROOTPATH.'imanager/tpl/');
define('IM_BACKUPPATH', IM_DATAPATH.'backups/');
define('IM_TEMPLATE_SUFFIX', '.tpl');
define('IM_VERSION', 300);
define('IM_VERSION_HUMAN', '3.0.0');


//define('IM_CONFIG_FILE', IM_SETTINGS_DIR.'config.im.xml');
//define('IM_SITE_URL', $SITEURL);
//define('IM_LANGUAGE', $LANG);
//define('IMTITLE', 'ItemManager');