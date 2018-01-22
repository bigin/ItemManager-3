<?php include(dirname(__DIR__).'/framework/imanager.php');

$category = new \Imanager\Category();
$category->set('name', 'My Bla Category');
$category->save();
