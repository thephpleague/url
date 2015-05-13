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

<p class="message-warning">No component delimiter should be submitted to the class constructors as they will be interpreted as part of the component value.</p>

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

## Components string representations

Each class provides several ways to represent the component value as string.

### String representation

Returns the string representation of the URL component. This is the form used when echoing the URL component from its getter methods. No component delimiter is returned.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://www.example.com:81/foo/bar?q=yolo#');
$url->getScheme()->__toString();  //returns 'http'
$url->getUser()->__toString();    //returns ''
$url->getPass()->__toString();    //returns ''
$url->getHost()->__toString();    //returns '[:11]'
$url->getPort()->__toString();    //returns '81'
$url->getPath()->__toString();    //returns '/foo/bar'
$url->getQuery()->__toString();   //returns 'q=yolo'
url->getFragment()->__toString(); //returns null
~~~

### URL-like representation

Returns the string representation of the URL component with its optional delimiters. This is the form used by the `Url::__toString` method when building the URL string representation.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://www.example.com:81/foo/bar?q=yolo#');
$url->getScheme()->getUriComponent();  //returns 'http:'
$url->getUser()->getUriComponent();    //returns ''
$url->getPass()->getUriComponent();    //returns ''
$url->getHost()->getUriComponent();    //returns '[:11]'
$url->getPort()->getUriComponent();    //returns ':81'
$url->getPath()->getUriComponent();    //returns '/foo/bar'
$url->getQuery()->getUriComponent();   //returns '?q=yolo'
url->getFragment()->getUriComponent(); //returns ''
~~~

## Components comparison

To compare two components to know if they represent the same value you can use the `Component::sameValueAs` method which compares them according to their respective `Component::getUriComponent` methods.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=I+am');
$url2 = Url::createFromUrl('http://www.example.com/hellow/./wor%20ld?who=I%20am;');

$url2->getQuery()->sameValueAs($url1->getQuery()); //returns true;
$url2->getPath()->sameValueAs($url1->getQuery());  //returns false;

$url1->getQuery->sameValueAs($url2);
//PHP Fatal Error Url and Component objects do not share the same interface
~~~

<p class="message-warning">Only components objects can be compared with each other, any other object or type will result in a Fatal error.</p>

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

## Complex components

The methods describe above works on all type of component but for more complex components care has be taken to provide more useful methods to interact with their value. Additional methods and properties were added to the following classes:

* `League\Url\Host` which deals with [the host component](/dev-master/components/host/);
* `League\Url\Path` which deals with [the path component](/dev-master/components/path/);
* `League\Url\Query` which deals with [the query component](/dev-master/components/query/);
