---
layout: default
title: URL Components
---

# URL parts and components

An URL string is composed of 8 components and 5 parts:

~~~
foo://example.com:8042/over/there?name=ferret#nose
\_/   \______________/\_________/ \_________/ \__/
 |           |            |            |        |
scheme   authority       path        query   fragment
~~~

The URL authority part in itself can be composed of up to 3 parts.

~~~
john:doe@example.com:8042
\______/ \_________/ \__/
    |         |        |
userinfo    host     port
~~~

The userinfo part is composed of the `user` and the `pass` components.

Apart from the authority part, each component and part of an URL is manageable by a dedicated class:

- The `League\Url\Scheme` class represents the URL scheme component;
- The `League\Url\UserInfo` class represents the URL userinfo part;
- The `League\Url\Component` class represents the URL user and pass component;
- The `League\Url\Host` class represents the URL host component;
- The `League\Url\Port` class represents the URL port component;
- The `League\Url\Path` class represents the URL path component;
- The `League\Url\Query` class represents the URL query component;
- The `League\Url\Fragment` class represents the URL fragment component;

Those classes share common methods to view and update their values. And just like the `League\Url\Url` class, they are defined as immutable value objects.

## URL part instantiation

Each component class can be instantiated independently from the main `League\Url\Url` object. They all expect a valid string according to their component validation rules as explain in RFC3986 or a Object with a `__toString()` method. If the value is invalid an `InvalidArgumentException` is thrown.

<p class="message-warning">No component delimiter should be submitted to the class constructors as they will be interpreted as part of the component value.</p>

~~~php
use League\Url;

$scheme    = new Url\Scheme('http');
$user      = new Url\Component('john');
$pass      = new Url\Component('doe');
$user_info = new Url\UserInfo($user, $pass);
$host      = new Url\Host('127.0.0.1');
$port      = new Url\Port(443);
$path      = new Url\Path('/foo/bar/file.csv');
$query     = new Url\Query('q=url&site=thephpleague');
$fragment  = new Url\Fragment('paragraphid');
~~~

### URL part status

At any given time you may want to know if the URL part is considered empty or not. To do so you can used the `UrlPart::isEmpty` method like shown below:

~~~php
use League\Url;

$scheme = new Url\Scheme('http');
$scheme->isEmpty(); //returns false;

$port = new Url\Port();
$port->isEmpty(); //return true;
~~~

## URL part representations

Each class provides several ways to represent the component value as string.

### String representation

The `Component::__toString` method returns the string representation of the URL part. This is the form used when echoing the URL component from the `League\Url\Url` getter methods. No component delimiter is returned.

~~~php
use League\Url;

$scheme = new Url\Scheme('http');
echo $scheme->__toString(); //displays 'http'

$userinfo = new Url\UserInfo('john');
echo $userinfo->__toString(); //displays 'john'

$path = new Url\Path('/toto le heros/file.xml');
echo $path->__toString(); //displays '/toto%20le%20heros/file.xml'
~~~

### URL-like representation

The `Component::getUriComponent` Returns the string representation of the URL part with its optional delimiters. This is the form used by the `Url::__toString` method when building the URL string representation.

~~~php
use League\Url;

$scheme = new Url\Scheme('http');
echo $scheme->getUriComponent(); //displays 'http:'

$userinfo = new Url\UserInfo('john');
echo $userinfo->getUriComponent(); //displays 'john@'

$path = new Url\Path('/toto le heros/file.xml');
echo $path->getUriComponent(); //displays '/toto%20le%20heros/file.xml'
~~~

## URL parts comparison

To compare two components to know if they represent the same value you can use the `Component::sameValueAs` method which compares them according to their respective `Component::getUriComponent` methods.

~~~php
use League\Url;

$host1    = new Url\Host('www.ExAmPLE.com');
$host2    = new Url\Host('www.example.com');
$fragment = new Url\Fragment('www.example.com');
$url      = new Url\Url::createFromUrl('www.example.com');

$host1->sameValueAs($host2); //returns true;
$host1->sameValueAs($fragment); //returns false;
$host1->sameValueAs($url);
//PHP Fatal Error Host and URL do not share the same interface
~~~

<p class="message-warning">Only Url parts objects can be compared with each others, any other object or type will result in a PHP Fatal Error.</p>

## Component modification

Each URL component class can have its content modified using the `modify` method. This method expects a string or an object with the `__toString` method.

<p class="message-warning">Because the <code>UserInfo</code> class represent a URL part it does not include a <code>modify</code> method.</p>

~~~php
use League\Url;

$query     = new Url\Query('q=url&site=thephpleague');
$new_query = $query->modify('q=yolo');
echo $new_query; //displays 'q=yolo'
echo $query();   //display 'q=url&site=thephpleague'
~~~

Since we are using immutable value objects, the source component is not modified instead a modified copy of the original object is returned.

## Complex Url parts

The methods describe above work on all type of URL parts but for more complex parts/component care has be taken to provide more useful methods to interact with their values. Additional methods and properties were added to the following classes:

* `League\Url\Scheme` which deals with [the scheme component](/dev-master/components/scheme/);
* `League\Url\Host` which deals with [the host component](/dev-master/components/host/);
* `League\Url\Port` which deals with [the port component](/dev-master/components/port/);
* `League\Url\Path` which deals with [the path component](/dev-master/components/path/);
* `League\Url\Query` which deals with [the query component](/dev-master/components/query/);
* `League\Url\UserInfo` which deals with [the URL user information part](/dev-master/components/userinfo/);
