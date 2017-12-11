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
$catMapper = imanager()->getCategoryMapper();
$catMapper->init();
$secondCategory = $catMapper->getCategory(1);

// Update an existing category
/*$catMapper = imanager()->getCategoryMapper();
$catMapper->init(1);
$secondCategory = $catMapper->getCategory(2);
$secondCategory->name = 'My Second Category Updated';
$secondCategory->save();*/



var_dump($secondCategory);