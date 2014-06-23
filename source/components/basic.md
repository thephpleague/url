---
layout: layout
title: URL Components
---

# URL components

Each component class implements the `League\Url\Components\ComponentInterface` with the following public methods:

* `set($data)`: set the component data
* `get()`: returns `null` if the class data is empty or its string representation
* `__toString()`: return a typecast string representation of the component.
* `getUriComponent()`: return an altered string representation to ease URL representation.

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;

~~~.language-php
use League\Url\Components\Scheme;

$scheme = new Scheme;
$scheme->get(); //will return null since no scheme was set
echo $scheme; // will echo '' an empty string
echo $scheme->getUriComponent(); //will echo '//'
$scheme->set('https');
echo $scheme->__toString(); //will echo 'https'
echo $scheme->getUriComponent(); //will echo 'https://'
~~~

The URL components objects that **only** implement this interface are:

* `League\Url\Components\Scheme` which deals with URL scheme component;
* `League\Url\Components\User` which deals with URL user component;
* `League\Url\Components\Pass` which deals with URL pass component;
* `League\Url\Components\Port` which deals with URL port component;
* `League\Url\Components\Fragment` which deals with URL fragment component;

The objects only differ in the way they validate and/or output the components.