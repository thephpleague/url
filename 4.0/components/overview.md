---
layout: default
title: URI Components and Parts
---

# URI parts and components

An URI string is composed of 8 components and 5 parts:

~~~
 foo://example.com:8042/over/there?name=ferret#nose
 \_/   \______________/\_________/ \_________/ \__/
  |           |            |            |        |
scheme     authority       path        query   fragment
  |   _____________________|__
 / \ /                        \
 urn:example:animal:ferret:nose
~~~

The URI authority part in itself can be composed of up to 3 parts.

~~~
john:doe@example.com:8042
\______/ \_________/ \__/
    |         |        |
userinfo    host     port
~~~

The userinfo part is composed of the `user` and the `pass` components.

~~~
captain:future
\_____/ \____/
   |      |
  user   pass
~~~

Apart from the authority part, each component and part of the URI is manageable through a dedicated interface:

- The `League\Uri\Interfaces\Components\UriPart` handles any URI part;
- The `League\Uri\Interfaces\Components\Component` extends the UriPart interface to handle components;
- The `League\Uri\Interfaces\Components\LiteralAccess` to enable access to the literal representation of the URI component;

In the library, all concrete classes that represent a URI part or component implements one or several of those interfaces. Just like the URI objects, these classes are defined as immutable value objects.

## URI part instantiation

Each component class can be instantiated independently from the main `League\Uri\Url` object.

They all expect:

- an **encoded** string according to the component validation rules as explain in RFC3986
- an object with a `__toString()` method.

<p class="message-warning">If the submitted value is invalid an <code>InvalidArgumentException</code> exception is thrown.</p>

<p class="message-warning">No component or uri part delimiter should be submitted to the classes constructor as they will be interpreted as part of the component value.</p>

~~~php
use League\Uri\Components;

$scheme   = new Components\Scheme('http');
$user     = new Components\User('john');
$pass     = new Components\Pass('doe');
$userInfo = new Components\UserInfo($user, $pass);
$host     = new Components\Host('127.0.0.1');
$port     = new Components\Port(443);
$path     = new Components\HierarchicalPath('/foo/bar/file.csv');
$dataPath = new Components\DataPath('data:,');
$query    = new Components\Query('q=url&site=thephpleague');
$fragment = new Components\Fragment('paragraphid');
~~~

### URI part status

At any given time you may want to know if the URI part is considered empty or not. To do so you can used the `UrlPart::isEmpty` method like shown below:

~~~php
use League\Uri\Components;

$scheme = new Components\Scheme('http');
$scheme->isEmpty(); //return false;

$port = new Components\Port();
$port->isEmpty(); //return true;
~~~

## URI part representations

Each class provides several ways to represent the component value as string.

### String representation

The `__toString` method returns the string representation of the object. This is the form used when echoing the URI component from the `League\Uri\Url` getter methods. No component delimiter is returned.

~~~php
use League\Uri\Components;

$scheme = new Components\Scheme('http');
echo $scheme->__toString(); //displays 'http'

$userinfo = new Components\UserInfo('john');
echo $userinfo->__toString(); //displays 'john'

$path = new Components\Path('/toto le heros/file.xml');
echo $path->__toString(); //displays '/toto%20le%20heros/file.xml'
~~~

### URI-like representation

The `getUriComponent` Returns the string representation of the URI part with its optional delimiters. This is the form used by the URI object `__toString` method when building the URI string representation.

~~~php
use League\Uri\Components;

$scheme = new Components\Scheme('http');
echo $scheme->getUriComponent(); //displays 'http:'

$userinfo = new Components\UserInfo('john');
echo $userinfo->getUriComponent(); //displays 'john@'

$path = new Components\Path('/toto le heros/file.xml');
echo $path->getUriComponent(); //displays '/toto%20le%20heros/file.xml'
~~~

### Component literal representation

The string representation will always return an encoded string. If you want to use the literal representation of one component you need to use the `getLiteral` method attached to it.

~~~php
use League\Uri\Components;

$user = new Components\User('foo%2Fbar');
echo $user->getLiteral(); //displays 'foo/bar'
echo $user->__toString(); //displays 'foo%2Fbar'
~~~

<p class="message-notice">Only the following components support the <code>getLiteral</code> methods: <code>Scheme</code>, <code>User</code>, <code>Pass</code>, <code>Host</code>, <code>Fragment</code>.</p>

## URI parts comparison

To compare two components to know if they represent the same value you can use the `sameValueAs` method which compares them according to their respective `getUriComponent` methods.

~~~php
use League\Uri\Schemes\Http;
use League\Uri\Components;

$host     = new Components\Host('www.ExAmPLE.com');
$alt_host = new Components\Host('www.example.com');
$fragment = new Components\Fragment('www.example.com');
$uri      = Http::createFromString('www.example.com');

$host->sameValueAs($alt_host); //return true;
$host->sameValueAs($fragment); //return false;
$host->sameValueAs($uri);
//a PHP Fatal Error is issue or a PHP7+ TypeError is thrown
~~~

<p class="message-warning">Only Url parts objects can be compared with each others, any other object will result in a PHP Fatal Error or a PHP7+ TypeError will be thrown.</p>

## Component modification

Each URI component class can have its content modified using the `modify` method. This method expects:

- a string;
- an object with the `__toString` method;
- or the `null`value;

<p class="message-warning">The <code>UserInfo</code> class does not include a <code>modify</code> method.</p>

~~~php
use League\Uri\Components;

$query     = new Components\Query('q=url&site=thephpleague');
$new_query = $query->modify('q=yolo');
echo $new_query; //displays 'q=yolo'
echo $query;     //display 'q=url&site=thephpleague'
~~~

Since we are using immutable value objects, the source component is not modified instead a modified copy of the original object is returned.

## Complex Url parts

For more complex parts/components care has be taken to provide more useful methods to interact with their values. Additional methods and properties were added to the following classes:

* `League\Uri\Components\UserInfo` which handles [the URI user information part](/4.0/components/userinfo/);
* `League\Uri\Components\Host` which handles [the host component](/4.0/components/host/);
* `League\Uri\Components\Port` which handles [the port component](/4.0/components/port/);
* `League\Uri\Components\HierarchicalPath` which handles [the path component](/4.0/components/path-hierarchical/);
* `League\Uri\Components\Query` which handles [the query component](/4.0/components/query/);
