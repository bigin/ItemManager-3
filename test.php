<?php

include 'index.php';

//var_dump($imanager->config);

// Creating new categories
/*$category = new \Imanager\Category();
$category->set('name', 'My First Category');
$category->set('slug', 'My First Category');
$category->save();*/

//var_dump($category);

// Get an existing category by id
/*$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);
Imanager\Util::preformat($secondCategory);*/

// Update an existing category
/*$imanager = imanager();
$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(2);
$secondCategory->name = 'My Second Category Updated';
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
$fieldMapper->init(1);*/

// Update field
/*$catMapper = $imanager->getCategoryMapper();
$catMapper->init();
$category = $catMapper->getCategory(1);
$fieldMapper = $imanager->getFieldMapper();
$fieldMapper->init(1);
$field = $fieldMapper->getField(1);
$field->position = 1;
$field->info = 'Just a simple field info';
$field->save();*/


Imanager\Util::preformat($fieldMapper->fields);



/**
 * Working with items
 */
// Create an item
/*$item = new \Imanager\Item(1);
$item->data = 'This is the item data to save';
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
