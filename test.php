<?php

include 'index.php';
// Ja gerade geändert1515662883
//$category = $imanager->getCategory('name=Ja gerade geändert151566%');
//\Imanager\Util::preformat($category);

/**
 * Working with config
 */
//var_dump($imanager->config);
//echo imanager('config')->maxFieldNameLength;

// Creating new categories
/*$category = new \Imanager\Category();
$category->set('name', 'My Thrid Category');
$category->save();*/

// Creating multiple categories in a loop
/*$number = 20;
for($i = 0; $i < $number; $i++) {
	$ran = rand(4, 1000000);
	$category = new \Imanager\Category();
	$category->set('name', 'My Category '.$ran);
	$category->set('slug', 'My Category '.$ran);
	$category->save();
}*/

//var_dump($category);

// Get an existing category by id and related items
/*$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);
$items = $secondCategory->getItems('test_field=');
Imanager\Util::preformat($items);*/

// Update an existing category
/*$catMapper = $imanager->categoryMapper;
$catMapper->init();
$secondCategory = $catMapper->getCategory(3);
$secondCategory->name = 'My Third Category';
$secondCategory->save();*/


/**
 * Working with fields
 */
// Create a single fields for a category
/*$imanager = imanager();
$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$category = $catMapper->getCategory(1);
$newField = new \Imanager\Field($category->id);
$newField->set('type', 'text');
$newField->set('name', 'text');
$newField->set('label', 'Text field');
$newField->save();
Imanager\Util::preformat($newField);*/

// Load fields of a category
/*$catMapper = $imanager->getCategoryMapper();
$catMapper->init();
$category = $catMapper->getCategory(1);
$fieldMapper = $imanager->getFieldMapper();
$fieldMapper->init(1);
Imanager\Util::preformat($fieldMapper->fields);*/

// Update field
/*$catMapper = $imanager->getCategoryMapper();
$catMapper->init();
$category = $catMapper->getCategory(1);
$fieldMapper = $imanager->getFieldMapper();
$fieldMapper->init(1);
$field = $fieldMapper->getField(1);
$field->position = 1;
$field->info = 'Just a simple field info '.time();
$field->save();
Imanager\Util::preformat($fieldMapper->fields);*/

/**
 * Working with items
 */
// Create an item
/*$item = new \Imanager\Item(1);
$item->data = 'This is the third item';
$item->save();*/

// Load an item
/*$mapper = imanager()->getItemMapper();
$mapper->init(1);
$item = imanager()->getItemMapper()->getItem(1);
Imanager\Util::preformat($item);*/

// Update item
/*$mapper = imanager()->getItemMapper();
$mapper->init(1);
$item = imanager()->getItemMapper()->getItem(1);
$item->set('data', 'Data wurde aktualisiert '.time());
$item->set('text', 'Das ist Itemtext-Value '.time());
$item->save();
$msgs = \Imanager\MsgReporter::getMessages();
if($msgs) {
	echo '<ul>';
	foreach($msgs as $msg) {
		echo $msg->text;
	}
	echo '</ul>';
}
Imanager\Util::preformat($item);*/

/*$mapper = imanager()->getItemMapper();
$mapper->init(1);
$item = imanager()->getItemMapper()->getItem(2);
$item->set('data', 'This is the second item item '.time());
$item->save();
$msgs = \Imanager\MsgReporter::getMessages();
if($msgs) {
	echo '<ul>';
	foreach($msgs as $msg) {
		echo $msg->text;
	}
	echo '</ul>';
}
Imanager\Util::preformat($item);*/

// Creating multiple items in a loop
/*$number = 200;
for($i = 0; $i < $number; $i++) {
	$ran = rand(4, 1000000);
	$item = new \Imanager\Item(1);
	$item->set('name', 'Automatically added item '.$ran);
	$item->set('label', 'Automatically added item '.$ran);
	$item->set('data', 'Automatically added data field value '.$ran);
	$item->save();
}
$msgs = \Imanager\MsgReporter::getMessages();
if($msgs) {
	echo '<ul>';
	foreach($msgs as $msg) {
		echo $msg->text;
	}
	echo '</ul>';
}*/

// Searching items
$category = $imanager->getCategory(1);

//$items = $category->items;

$start = isset($_GET['page']) ? (($_GET['page'] -1) * $imanager->config->maxItemsPerPage +1) : 1;

// $start, $imanager->config->maxItemsPerPage

$res = $category->getItems('', $start, $imanager->config->maxItemsPerPage);
//$res = $category->sort('position', 'asc',  $start, $imanager->config->maxItemsPerPage);

foreach($res as $item) {
	echo 'ID: '.$item->id .'  Name: '. $item->name.'<br>';
}


$tp = new \Imanager\TemplateParser($imanager);
$tp->init();
$pagination = $tp->renderPagination($res);

echo $pagination;

//$imanager->paginate($items, );

//$items = $category->getItems('label=%added itäßm 116336-||data=Automatically added data field value 976418');

//echo $pagination;
//\Imanager\Util::preformat($items);