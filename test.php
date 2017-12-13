<?php

include 'index.php';

//var_dump($imanager->config);

// Creating new categories
/*$category = new \Imanager\Category();
$category->set('name', 'My Second Category');
$category->set('slug', 'My Second Category');
$category->save();*/

//var_dump($category);

// Get an existing category by id
/*$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);*/

// Update an existing category
/*$imanager = imanager();
$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);
$secondCategory->name = 'My First Category Updated';
$secondCategory->save();*/


// Create fields for a category
/*$imanager = imanager();
$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);

$fields = array(

	'cat' => $secondCategory->id,

	'cf_0_key'   => 'data',
	'cf_0_label' => 'Data field',
	'cf_0_type'  => 'text',
	'cf_0_options' => '',
	'cf_0_value' => ''
);
// Create fields
if($imanager->createFields($fields) !== true) {
	$msgs = \Imanager\MsgReporter::getMessages();
	if($msgs) {
		echo '<ul>';
		foreach($msgs as $msg) {
			echo $msg->text;
		}
		echo '</ul>';
	}
} else {
	// Set category field data
	$fieldsdata = array(
		array(
			'field' => 1,
			'default' => '',
			'info' => 'Please never change or delete this data!',
			'required' => true,
			'min_field_input' => 0,
			'max_field_input' => 0,
			'cssclass' => 'readonly'
		)
	);

	// Set field data
	if($imanager->setFiedData($secondCategory->id, $fieldsdata) !== true) {
		$msgs = \Imanager\MsgReporter::getMessages();
		if($msgs) {
			echo '<ul>';
			foreach($msgs as $msg) {
				echo $msg->text;
			}
			echo '</ul>';
		}
	} else {
		$msgs = \Imanager\MsgReporter::getMessages();
		if($msgs) {
			echo '<ul>';
			foreach($msgs as $msg) {
				echo $msg->text;
			}
			echo '</ul>';
		}
	}
}*/



Imanager\Util::preformat($secondCategory);