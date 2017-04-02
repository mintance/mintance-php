# Mintance PHP Library
This library provides an API to track events and update profiles on Mintance.

Install with Composer
------------
Add mintance/mintance-php as a dependency and run composer update

```json
"require": {
    ...
    "mintance/mintance-php" : "1.*"
    ...
}
```

Now you can start tracking events and people:

```php
<?php
// import dependencies
require 'vendor/autoload.php';

// create Mintance class, replace with your project token
$mt = new Mintance("PROJECT_TOKEN");

// track an event
$mt->track("button clicked", array("label" => "sign-up")); 

// create/update a profile for user id 12345
$mt->people->set(array(
    '$first_name'       => "John",
    '$last_name'        => "Doe",
    '$email'            => "john.doe@example.com",
    '$phone'            => "5555555555",
    "Favorite Color"    => "red"
));
```


Install Manually
------------
 1. <a href="https://github.com/mintance/mintance-php/archive/master.zip">Download the Mintance PHP Library</a>
 2.  Extract the zip file to a directory called "mintance-php" in your project root
 3.  Now you can start tracking events and people:

```php
<?php
// import Mixpanel
require 'mintance-php/src/Mintance.php';

// create Mintance class, replace with your project token
$mt = new Mintance("PROJECT_TOKEN");

// track an event
$mt->track("button clicked", array("label" => "sign-up")); 

// create/update a profile for user id 12345
$mt->people->set(array(
    '$first_name'       => "John",
    '$last_name'        => "Doe",
    '$email'            => "john.doe@example.com",
    '$phone'            => "5555555555",
    "Favorite Color"    => "red"
));
```