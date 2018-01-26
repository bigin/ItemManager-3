# IManager simple flat-file PHP framework
_Ideal for smaller projects with smaller-sized data volumes, because no database is used._

Project is currently in development stage. Please check back here frequently, hopefully here will be continuous updates from time to time ;-)

## Here are some tips for getting started

- [Configure IManager](#configure-imanager)    
    - [.htaccess File](#htaccess-file)
    - [Global Configuration](#global-config) 
- [Design](#design)   
- [File Structure](#file-structure)   
- [Getting Started](#getting-started)
    - [Working With Categories](#working-with-categories)   
    - [Working With Fields](#working-with-fields) 
    - [Working With Items](#working-with-items)
        - [Accessing Fields/Attributes](#accessing-fieldsattributes)
        - [Retrieving Items & Creating A Number Of Items](#retrieving-items-creating-a-number-of-items)    



## Configure Imanager

NOTE: Before you start using IManager, you must set your file permissions to allow IManager access and write into the complete `data/` directory except `data/config` folder.

It is recommended the 755 permission for writable directories and 644 permission for writable files, as a starting point.

Please be careful with file permissions like 777 or 666. Those permissions effectively make the files readable and writable to all accounts on the server.


##### .htaccess file
Please make sure that the `.htaccess` file that comes with IManager, is in IManager's root folder – it's usually where your `imanager.php` file is locaded. 

##### Global config   
When you run IManager, it automatically includes your project-specific global configuration entries stored in `custom.config.php` file. By default, there is no custom.config.php file in your `/your-project-dir/data/settings/` directory, you have to create this file first. The directory `/your-project-dir/imanager/inc/` contains a `config.php` file (This is the default configuration file and should not be changed). You will need to make a copy of this file, place it in the `/your-project-dir/data/settings/` directory and re-name it to `custom.config.php`, that one will take priority over default IManager settings. Now, you can modify all variables listed in the custom.config.php suit your needs.

## Design

The IManager is consist of three main objects:
1. **Categories**    
2. **Fields**
3. **Items**
 
The IManager's categories represent a kind of basic structure or a type of data template. Each item or field you create must belong to a category. Fields are used to determine the properties of items in a category and to control the input, storage and output of the content. IManager's fields actually consist of two separate entities: field and input. There are several field types that you can use to customize your items according to your needs.

## File Structure

File structure of IManager is not completely created yet and I am still making some changes...

## Getting Started
_An overview of Imanager, how to download and use, basics and examples_

Use IManager's API in any other PHP scripts it's easy!
The first thing you should do is just including IManager's `./imanager.php` file from any other PHP script. IManager comes with an index.php file in the root directory by default. This file is not needed to run IManager and you can safely delete it, but look at this file anyway, it represent an example of how to include IManager in your script.

```php
include('/your-imanager-location/imanager.php');
```

Once you have included IManager like in the example above, the API is now available to you in the $imanager global variable, or via the imanager() function. For example, here's how you would access the `systemDateFormat` config variable now:

```php
echo $imanager->config->systemDateFormat;
```

or
```php
echo imanager('config')->systemDateFormat;
```


## Working with categories

Create a new category:
```php
$category = new \Imanager\Category();
$category->set('name', 'My First Category');
$category->save();
```

Another example, create a category named My Test Category if it does not yet exist:
```php
$category = $imanager->getCategory('name=My Test Category');
if(!$category) {
    $category = new \Imanager\Category();
    $category->set('name', 'My Test Category');
    $category->save();
}
```


#### Category attributes
You can use any of the following category attributes:
- **name** (A string with a maximum length of 255 characters)
- **id** (An integer automatically generated when creating categories)
- **position** (An integer, if not specified it's created automatically when creating categories)
- **slug** (An ASCII string with a maximum length of 255 characters. If not specified it's created automatically from *name* when creating categories)
- **created** (Automatically generated timestamp)
- **updated** (Automatically generated timestamp)

To set or change a category attribute value, use `Category::set()` method:
```php
$category->set('slug', 'my-test-category');
```

Do not forget to save after that:
```php
$category->save();
```

To load a specific category, use IManager's `getCategory()` method:
```php
$category = $imanager->getCategory('name=My Test Category');
```
or using an ID:
```php
$category = $imanager->getCategory(1);
```
using any other category attribute:
```php
$category = $imanager->getCategory('slug=my-test-category');
```
using multiple attributes:
```php
$category = $imanager->getCategory('slug=my-test-category&&name=My Test Category');
```

If you want to select multiple categories use `getCategories()` instead. For example, to select all the categories created within the last week, you can use folowing selector:
```php
$now = time();
$lastWeek = $now - (7 * 24 * 60 * 60);
$category = $imanager->getCategories("created>=$lastWeek&&created<$now");
```

You can always access category attributes by referencing them from the $category variable directly:
```php
$category = $imanager->getCategory('name=My Test Category');
// Accessing slug
echo "Category Slug: $category->slug<br>";
// Created date
echo 'Created Date: '.date('Y-m-d H:i', $category->created).'<br>';
```

Create another category:
```php
$category = $imanager->getCategory('name=Another One Category');
if(!$category) {
    $category = new \Imanager\Category();
    $category->set('name', 'Another One Category');
    if($category->save()) {
        echo "A new category named <strong>$category->name</strong> has been created<br>";
    }
}
```

Delete recently created category again:
```php
$category = $imanager->getCategory('name=Another One Category');
if($category) {
    if($imanager->remove($category)) {
        echo "Category successfully deleted";
    }
}
```

## Working with fields

Create a single field of type text for our category, let's name it `url`:
```php
$category = $imanager->getCategory('name=My Test Category');
$newField = new \Imanager\Field($category->id);
$newField->set('type', 'text');
$newField->set('name', 'url');
$newField->set('label', 'Insert any URL');
$newField->save();
```

To see if the field was created correctly, you can use native PHP `var_dump()` function. Instead, I will use Imanager's `Util::preformat()` method, which allows to display the object structure:
```php
\Imanager\Util::preformat($category->fields);
```

The output might look like this:
```
Array
(
    [url] => Imanager\Field Object
        (
            [categoryid] => 2
            [id] => 1
            [name] => url
            [label] => Insert any URL
            [type] => text
            [position] => 1
            [default] => 
            [options] => Array
                (
                )

            [info] => 
            [required] => 
            [minimum] => 0
            [maximum] => 0
            [cssclass] => 
            [configs] => Imanager\FieldConfigs Object
                (
                )

            [created] => 1516743495
            [updated] => 1516743495
        )
)
```

If the output works, go on and create more fields for your category: A field of type `password` with the same name `password`, and one more text field for storing user's firstname:
```php
$category = $imanager->getCategory('name=My Test Category');

$newField = new \Imanager\Field($category->id);
$newField->set('type', 'password');
$newField->set('name', 'password');
$newField->set('label', 'Enter your password here');
$newField->save();

$newField = new \Imanager\Field($category->id);
$newField->set('type', 'text');
$newField->set('name', 'firstname');
$newField->set('label', 'Enter your firstname');
$newField->save();
```

Ok, if you call `\Imanager\Util::preformat($category->fields)` method, you'll see that you currently have fields listed in the following order:
```
1. url
2. password
3. firstnanme
```

Normally it doesn't matter, except that we wanted to build a UI later on, and want to display the fields in the correct order.
The order can be changed by adjusting the position of the fields as follows:
```php
$category = $imanager->getCategory('name=My Test Category');

$field = $category->getField('name=firstname');
$field->set('position', 1);
$field->save();

$field = $category->getField('name=password');
$field->set('position', 2);
$field->save();

$field = $category->getField('name=url');
$field->set('position', 3);
$field->save();
```

It couldn't be easier!

Create another one:
```php
$category = $imanager->getCategory('name=My Test Category');
$newfield = new \Imanager\Field($category->id);

$newfield->set('name', 'test_field');
$newfield->set('type', 'text');
$newfield->save();
```

If you want to remove the field, proceed as follows:
```php
$category = $imanager->getCategory('name=My Test Category');

$field = $category->getField('name=test_field');
if($field) {
    $category->remove($field);
}
```



#### Field attributes
All fields contain these standard attributes, which you can access any time:
- **categoryid** (An integer, generated when creating field)
- **id** (An integer automatically generated when creating field)
- **name** (An ASCII unique string with a maximum length defined in $config->maxFieldNameLength variable. No spaces, hyphens or periods are allowed except underscores)
- **label** (UTF-8 string)
- **type** (see [Field Types](./#field-types))
- **position** (An integer, if not specified it's created automatically when creating field)
- **default** (A default field value, NULL if not specified)
- **options** (An array of field options, used by certain fields)
- **required** (Boolean, default FALSE)
- **minimum** (Integer, minimum field value length)
- **maximum** (Integer, maximum field value length)
- **cssclass** (String)
- **configs** (FieldConfigs Object, used to customize field configs)
- **created** (Automatically generated timestamp)
- **updated** (Automatically generated timestamp)

#### Field types
These field types are currently available:
- **text** (Standard text field with a maximum length of 255 characters, but can also be used for longer texts)
- **slug** (Address of a specific page or post, with a max len. 250 characters, can't include special characters other than dashes -)
- **password** (Special field for storing passwords)
- **longtext** (A field for storing longer textes, a CKEditor can also be used)
- **hidden** (An alias of text field with a little FieldHidden class exception)
- **fileupload** (File or image upload field)
- **dropdown** (Dropdown/select field)
- **checkbox** (Checkbox)

## Working with items
Items includes both built-in attributes, which are common to all items and custom fields. The custom fields are those that you create manually and then assign to your category. Items also has several functions/methods that enable you to perform other tasks with it.

Let's start right away by creating a few items in our `My Test Category`category:
```php
// Get the category by name
$category = $imanager->getCategory('name=My Test Category');

// Create new item in your category
$item = new \Imanager\Item($category->id);
// Set name
$item->set('firstname', 'Annamae');
$item->set('url', 'http://annamae-homepage.com');
// Set pass
$result = $item->set('password', array(
        'password' => 'My secret password',
        'confirm_password' => 'My secret password'
    )
);
// Make sure that password is set correctly, then save
if($result !== true) {
    echo "Error code: $result<br>";
} else {
    if($item->save()) {
        echo "User $item->firstname is successfully saved!<br>";
    }
}


// Ok let's create another user
$item = new \Imanager\Item($category->id);
$item->set('firstname', 'Caden');
$item->set('url', 'http://cadens-page.org');
$result = $item->set('password', array(
        'password' => 'Blab',
        'confirm_password' => 'Blab'
    )
);
if($result !== true) {
    echo "Error code: $result<br>";
} else {
    if($item->save()) {
        echo "User $item->firstname is successfully saved!<br>";
    }
}
```
 
The method `$category->getItem('firstname=Annamae')` selects an item with the first name `Annamae`:
```php
$category = $imanager->getCategory('name=My Test Category');
if($category) {
    $item = $category->getItem('firstname=Annamae');
    if($item) {
        \Imanager\Util::preformat($item);
    }
}
```

The output could then look like this:
```
Imanager\Item Object
(
    [categoryid] => 2
    [id] => 1
    [name] => 
    [label] => 
    [position] => 1
    [active] => 
    [created] => 1516817566
    [updated] => 1516817566
    [firstname] => Annamae
    [url] => http://annamae-homepage.com
    [password] => Imanager\PasswordFieldValue Object
        (
            [password] => fd1e808efaf6c8bd75f8f4a4dae0b2ae4bc1d618
            [salt] => lJAFry5#ph
        )
)
```


###### Accessing fields/attributes
Unlike a previous version of IManager, it is now possible to access the custom fields directly, for example:
```php
...
$item = $category->getItem('firstname=Annamae');
if($item) {
    echo "User name: $item->firstname<br>";
    echo "User's website: $item->url<br>";
}
```

###### Retrieving items | Creating a number of items
There are several methods that could be used for retrieving items. By the way, all of these `get` methods always work in the same way for categories, fields or items, but may have a slightly different names. This means that you only have to remember them once and can use them in the same way in another context. We shall now add a few more items to our category so that we can try out different of this methods. Creating the new items is easiest done automatically, in a loop:

```php
$users = array(
    array(
        'name' => 'J. Wright',
        'firstname' => 'Daisey',
        'active' => true,
        'url' => 'http://infocpnsan.com'
    ),
    array(
        'name' => 'Lopez',
        'firstname' => 'Glenda',
        'active' => true,
        'url' => 'http://ggmrk.com'
    ),
    array(
        'name' => 'Perković',
        'firstname' => 'Kristijan',
        'active' => true,
        'url' => 'http://metaldefense.com'
    ),
    array(
        'name' => 'Altman',
        'firstname' => 'Michal',
        'active' => true,
        'url' => 'http://fripstorexp.com'
    ),
    array(
        'name' => 'Esselink',
        'firstname' => 'Toon',
        'active' => false,
        'url' => 'https://adoptingsafe.com'
    ),
);

$category = $imanager->getCategory('name=My Test Category');
if($category) {
    foreach($users as $user) {
        // Create new item
        $item = new \Imanager\Item($category->id);
        $item->set('name', $user['name']);
        $item->set('firstname', $user['firstname']);
        $item->set('url', $user['url']);
        $item->set('active', $user['active']);
        if($item->save()) {
            echo "A new user '$item->firstname' has been created<br>";
        }
    }
}
```

Well, let's select all users who haven't been activated yet i. e. have value of the `active` attribute set to `false`. If you want to check the boolean field set to `false`, you have to use one of the following selectors:
```php
$items = $category->getItems('active!='.true);
```

This one:
```php
$items = $category->getItems('active=');
```

Or this:
```php
$category->getItems('active='.false);
```

So in order to select the disabled users, proceed as follows:
```php
$category = $imanager->getCategory('name=My Test Category');
if($category) {
    $users = $category->getItems('active=');
}
...
```


