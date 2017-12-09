<?php

include 'index.php';

//var_dump($imanager->config);

// Creating new categories
/*$category = new \Imanager\Category();
$category->set('name', 'My Second Category');
$category->set('slug', 'My Second Category');
$category->save();*/

// Get an existing category by id
/*$catMapper = imanager()->getCategoryMapper();
$catMapper->init(1);
$secondCategory = $catMapper->getCategory(2);*/

// Update an existing category
/*$catMapper = imanager()->getCategoryMapper();
$catMapper->init(1);
$secondCategory = $catMapper->getCategory(2);
$secondCategory->name = 'My Second Category Updated';
$secondCategory->save();*/


var_dump($secondCategory);