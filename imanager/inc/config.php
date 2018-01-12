<?php defined('IS_IM') or die('you cannot load this page directly.');


/**
 * Max length for the fieds names
 */
$config->maxFieldNameLength = 30;

/**
 * Max length for the item names and labels
 */
$config->maxItemNameLength = 255;

/**
 * Pagination settings
 */
$config->maxItemsPerPage = 10;

/**
 * Create field backups when saving or editing fields
 */
$config->backupCategories = true;

/**
 * Create field backups when saving or editing fields
 */
$config->backupFields = true;

/**
 * Create items backups when saving or editing
 */
$config->backupItems = false;

/**
 * Backup file lifetime
 */
$config->minBackupTimePeriod = 2;

/**
 * Filter by category attribute
 */
$config->filterByCategories = 'position';

/**
 * Filter by field attribute
 */
$config->filterByFields = 'position';

/**
 * Filter by item attribute
 */
$config->filterByItems = 'position';

/**
 *	Permissions for wew directories
 */
$config->chmodDir = 0755;


$config->pageNumbersUrlSegment = 'page';


//$this->checkInstalled = true;

//$this->injectActions = false;

//$this->useAllocater = true;

//$this->hiddeAdmin = false;

//$this->adminDisabledMsg = 'ItemManager\'s admin interface is currently disabled';
