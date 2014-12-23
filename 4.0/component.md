---
layout: default
title: URL Components
---

# URL components

An URL string is composed of up to 8 components. The `League\Url` library provides interfaces and classes to interact with each URL component. The classes can all be use independently of a `League\Url\Interfaces\UrlInterface` implementing class.

## Component Interface

Each component class implements the `League\Url\Interfaces\ComponentInterface` with the following public methods:

* `set($data)`: set the component data
* `get()`: returns `null` if the class data is empty or its string representation
* `__toString()`: return a typecast string representation of the component.
* `getUriComponent()`: return an altered string representation to ease URL representation.
* `sameValueAs(ComponentInterface $component)`: return true if both components string representation values are equals.

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;

<h2 id="simple-components">Single Value Components</h2>

The URL components classes which represent single values only:

* implement the `League\Url\Interface\ComponentInterface` interface.
* differ in the way they validate and/or output the components.

These classes are:

* `League\Url\Scheme` which deals with the scheme component;
* `League\Url\User` which deals with the user component;
* `League\Url\Pass` which deals with the pass component;
* `League\Url\Port` which deals with the port component;
* `League\Url\Fragment` which deals with the fragment component;

Example using the `League\Url\Scheme` class:

~~~php
use League\Url\Scheme;

$scheme = new Scheme;
$scheme->get(); //will return null since no scheme was set
echo $scheme; // will echo '' an empty string
echo $scheme->getUriComponent(); //will echo '//'
$scheme->set('https');
echo $scheme->__toString(); //will echo 'https'
echo $scheme->getUriComponent(); //will echo 'https://'
~~~

<h2 id="complex-components">Multiple Values Components</h2>

Aside the simple components, a `League/Url` contains 3 more complex components namely:

* `League\Url\Query` [which which deals with the query component](/4.0/query/);
* `League\Url\Path` [which which deals with the path component](/4.0/path/);
* `League\Url\Host` [which which deals with the host component](/4.0/host/);

Each of these classes takes into account the specifity of its related component.
