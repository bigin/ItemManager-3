<?php

include 'imanager.php';
// Ja gerade geändert1515662883
//$category = $imanager->getCategory('name=Ja gerade geändert151566%');
//\Imanager\Util::preformat($category);

/**
 * Working with config
 */
//var_dump($imanager->config);
//echo imanager('config')->maxFieldNameLength;



// If categories are not available yet, create one
/*if(!$imanager->categoryMapper->categories) {
	$category = new \Imanager\Category();
	$category->set('name', 'My Fucking Test-Category');
	$category->set('slug', 'My Fucking Test-Category');
	$category->save();
}*/

/*$category = $imanager->getCategory('name=My Fucking Test-Category');
$newField = new \Imanager\Field($category->id);
$newField->set('type', 'text');
$newField->set('name', 'data');
$newField->set('label', 'Data field');
$newField->save();
\Imanager\Util::preformat('Just a test');*/

//\Imanager\Util::preformat($category);

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
/*$catMapper = $imanager->categoryMapper;
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);
$items = $secondCategory->getItems('test_field=');
Imanager\Util::preformat($items);*/

// Get several Categories
/*$categories = $imanager->getCategories('position>10&&position<=16');
\Imanager\Util::preformat($categories);*/

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

// Outputing items of a category with pagination
/*$category = $imanager->getCategory(1);
$items = $category->getItems('active='.false);
$result = $category->sort('position', 'asc',  0, $imanager->config->maxItemsPerPage, $items);
foreach($result as $item) {
	echo 'ID: '.$item->id .'  Name: '. $item->name.'<br>';
}
$pagination = $imanager->paginate($items);
echo $pagination;*/

// Remove an item with throwing an Exception
/*$category = $imanager->getCategory(1);
$item = $category->getItem(179);
if($item) {
	try {
		$category->remove($item);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	\Imanager\Util::preformat($item);
}*/

// Remove a field of the category 1, with throwing an Exception
/*$category = $imanager->getCategory(1);
$field = $category->getField('name=test_field');
if($field) {
	try {
		$category->remove($field);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	\Imanager\Util::preformat($field);
}*/

// Remove a Category, with throwing an Exception
/*$category = $imanager->getCategory(1);
if($category) {
	try {
		$imanager->remove($category);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	\Imanager\Util::preformat($category);
}*/


// Markup Cache is used for caching specific parts of your templates
/*if(!$output = $imanager->sectionCache->get('index')) {
	$category = $imanager->getCategory(1);
	$output = '';
	if($category->items) {
		foreach($category->items as $item) {
			$output .= "Item Name: {$item->name}<br>";
		}
	}
	$imanager->sectionCache->save($output);
}
echo $output;*/

