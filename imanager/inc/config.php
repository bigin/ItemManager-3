<?php defined('IS_IM') or die('you cannot load this page directly.');


/**
 * Backup files in days
 */
$config->minBackupTimePeriod = 2;

/**
 * Max length for the fieds names
 */
$config->maxFieldNameLength = 30;

/**
 * Create field backups?
 */
$config->backupFields = false;

/**
 * Filter by field attribute
 */
$config->filterByFields = 'position';


/**
 *	Permissions for wew directories
 */
$config->chmodDir = 0755;




$config->string2 = 'string2';

//$this->checkInstalled = true;

//$this->injectActions = false;

//$this->useAllocater = true;

//$this->hiddeAdmin = false;

//$this->adminDisabledMsg = 'ItemManager\'s admin interface is currently disabled';
