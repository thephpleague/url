---
layout: default
title: URL Components
---

# URL components

An URL string is composed of 8 components. Each of these components are returned from the `League\Url\Url` class getter methods as the following specific component classes:

- The `League\Url\Scheme` class represents the URL scheme component;
- The `League\Url\User` class represents the URL user component;
- The `League\Url\Pass` class represents the URL pass component;
- The `League\Url\Host` class represents the URL host component;
- The `League\Url\Port` class represents the URL port component;
- The `League\Url\Path` class represents the URL path component;
- The `League\Url\Query` class represents the URL query component;
- The `League\Url\Fragment` class represents the URL fragment component;

Those classes share common methods to view and update their values. And just like the `League\Url\Url` class, they are defined as immutable value objects.

## Component instantiation

Each component class can be instantiated independently from the main `League\Url\Url` object. They all expect a valid string according to their component validation rules as explain in RFC3986. If the value is invalid an `InvalidArgumentException` is thrown.

<p class="message-warning">No delimiter should be submitted to the component constructor as it will be interpreted as the first character of the component value.</p>

~~~php
use League\Url;

$scheme   = Url\Scheme('http');
$user     = Url\User('john');
$pass     = Url\Pass('doe');
$host     = Url\Host('127.0.0.1');
$port     = Url\Port(443);
$path     = Url\Path('/foo/bar/file.csv');
$query    = Url\Query('q=url&site=thephpleague');
$fragment = Url\Fragment('paragraphid');
~~~

## Component modification

Each component can have its content modified using the `withValue` method. This method expects a string or an object with the `__toString` method.

~~~php
use League\Url\Url;

$query     = Url\Query('q=url&site=thephpleague');
$new_query = $query->withValue('q=yolo');
echo $new_query; //displays 'q=yolo'
echo $query(); //display 'q=url&site=thephpleague'
~~~

Since we are using immutable value objects, the source component is not modified instead a modified copy of the original object is returned.

## Component representations

Beacuse of the way with interact with each component, each class provides several ways to represent the component value.

### Raw representation

Returns the raw representation of the URL component, the return value can be `null` if the component is not set.

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://[::1]:81/foo/bar?q=yolo#');
$url->getScheme()->get();    //returns 'http'
$url->getUser()->get();      //returns null
$url->getPass()->get();      //returns null
$url->getHost()->get();      //returns ':11'
$url->getPort()->get();      //returns 81 as a integer
$url->getPath()->get();      //returns '/foo/bar'
$url->getQuery()->get();     //returns 'q=yolo'
url->getFragment()->get();   //returns null
~~~

### String representation

Returns the string representation of the URL component. This is the form used when echoing the URL component from its getter methods.

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://www.example.com:81/foo/bar?q=yolo#');
$url->getScheme()->__toString();    //returns 'http'
$url->getUser()->__toString();      //returns ''
$url->getPass()->__toString();      //returns ''
$url->getHost()->__toString();      //returns '[:11]'
$url->getPort()->__toString();      //returns '81'
$url->getPath()->__toString();      //returns '/foo/bar'
$url->getQuery()->__toString();     //returns 'q=yolo'
url->getFragment()->__toString();   //returns null
~~~

### URL-like representation

Returns the string representation of the URL component with its optional delimiter. This is the form used by the `Url::__toString` method when building the URL string representation.

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://www.example.com:81/foo/bar?q=yolo#');
$url->getScheme()->getUriComponent();    //returns 'http:'
$url->getUser()->getUriComponent();      //returns ''
$url->getPass()->getUriComponent();      //returns ''
$url->getHost()->getUriComponent();      //returns '[:11]'
$url->getPort()->getUriComponent();      //returns ':81'
$url->getPath()->getUriComponent();      //returns '/foo/bar'
$url->getQuery()->getUriComponent();     //returns '?q=yolo'
url->getFragment()->getUriComponent();   //returns ''
~~~

## Component comparison

To compare two component to know if they represent the same ressource you can use the `Component::sameValueAs` method which compares two `Component` object according to their respective `Component::getUriComponent` methods.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=I+am');
$url2 = Url::createFromUrl('http://www.example.com/hellow/./wor%20ld?who=I%20am;');

$url2->getQuery()->sameValueAs($url1->getQuery()); //returns true;
$url2->getPath()->sameValueAs($url1->getQuery());  //returns false;
~~~

<p class="message-warning">Only components objects can be compared with each other, any other object or type will result in a Fatal error.</p>


## Complex components

The methods describe above works on all type of component but for more complex components care has be taken to provide more usefuls methods to interact with their data. To take into account their specifities additional methods and properties were added to the following classes:

* `League\Url\Host` which deals with [the host component](/dev-master/host/);
* `League\Url\Path` which deals with [the path component](/dev-master/path/);
* `League\Url\Query` which deals with [the query component](/dev-master/query/);


