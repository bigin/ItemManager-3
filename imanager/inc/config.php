<?php defined('IS_IM') or die('you cannot load this page directly.');


/**
 * Enable debug mode?
 *
 * Debug mode causes additional info to appear for use during dev and debugging.
 * This is almost always recommended for sites in development. However, you should
 * always have this disabled for live/production sites.
 *
 * @var bool
 *
 */
$config->debug = true;

/**
 * Max length for the fieds names
 *
 * @var integer
 */
$config->maxFieldNameLength = 30;

/**
 * Max length for the item names and labels
 *
 * @var integer
 */
$config->maxItemNameLength = 255;

/**
 * Pagination settings
 *
 * @var integer
 */
$config->maxItemsPerPage = 10;

/**
 * Create field backups when saving or editing fields
 *
 * @var bool
 */
$config->backupCategories = true;

/**
 * Create field backups when saving or editing fields
 *
 * @var bool
 */
$config->backupFields = true;

/**
 * Create items backups when saving or editing
 *
 * @var bool
 */
$config->backupItems = true;

/**
 * Backup file lifetime
 *
 * @var integer
 */
$config->minBackupTimePeriod = 2;

/**
 * Filter by category attribute
 *
 * @var string|bool|int
 */
$config->filterByCategories = 'position';

/**
 * Filter by field attribute
 *
 * @var string|bool|int
 */
$config->filterByFields = 'position';

/**
 * Filter by item attribute
 *
 * @var string|bool|int
 */
$config->filterByItems = 'position';

/**
 *	Permissions for wew directories
 *
 * @var octal
 */
$config->chmodDir = 0755;

/**
 * Url segment that should be used for the numbering of pages
 *
 * @var string
 */
$config->pageNumbersUrlSegment = 'page';


$config->systemDateFormat = 'd.m.Y - H:i:s';


//$this->injectActions = false;

//$this->useAllocater = true;

//$this->hiddeAdmin = false;

//$this->adminDisabledMsg = 'ItemManager\'s admin interface is currently disabled';
