---
layout: layout
title: URL Components
---

# URL components

An URL string is composed of up to 8 components. The `League\Url` library provides interfaces and classes to interact with each URL component. The classes can all be use independently of a `League\Url\UrlInterface` implementing class. 

## Component Interface

Each component class implements the `League\Url\Components\ComponentInterface` with the following public methods:

* `set($data)`: set the component data
* `get()`: returns `null` if the class data is empty or its string representation
* `__toString()`: return a typecast string representation of the component.
* `getUriComponent()`: return an altered string representation to ease URL representation.
* `sameValueAs(ComponentInterface $component)`: return true if both components string representation values are equals. *added in version 3.2+*

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;

<h2 id="simple-components">Single Value Components</h2>

The URL components classes which represent single values only:

* implement the `League\Url\Components\ComponentInterface` interface. 
* differ in the way they validate and/or output the components.

These classes are:

* `League\Url\Components\Scheme` which deals with the scheme component;
* `League\Url\Components\User` which deals with the user component;
* `League\Url\Components\Pass` which deals with the pass component;
* `League\Url\Components\Port` which deals with the port component;
* `League\Url\Components\Fragment` which deals with the fragment component;

Example using the `League\Url\Components\Scheme` class:

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

<h2 id="complex-components">Multiple Values Components</h2>

In addition to the `League\Url\Components\ComponentInterface`, classes that deal with multiple values components implement the following interfaces:

* `Countable`
* `IteratorAggregate`
* `ArrayAccess`
* `League\Url\Components\ComponentArrayInterface`

The `League\Url\Components\ComponentArrayInterface` adds the following methods:

* `toArray()`: will return an array representation of the component;
* `keys()`: will return all the keys or a subset of the keys of an array if a value is given.

<p class="message-info"><strong>Of note:</strong> The <code>$data</code> argument for the <code>set</code> method can also be an <code>array</code> or a <code>Traversable</code> object.</p>

The URL components classes implementing these interfaces are:

* `League\Url\Components\Query` [which which deals with the query component](/components/query/);
* `League\Url\Components\Path` [which which deals with the path component](/components/path/);
* `League\Url\Components\Host` [which which deals with the host component](/components/host/);

<h3 id="segment-components">Segment Values Components</h3>

Segment values components like [League\Url\Components\Path](/components/path/) and 
[League\Url\Components\Host](/components/host/) also implement the `League\Url\Components\SegmentInterface` interface which adds the following methods:

* `append($data, $whence = null, $whence_index = null)`: append data into the component;
* `prepend($data, $whence = null, $whence_index = null)`: prepend data into the component;
* `remove($data)`: remove data from the component;

The arguments:

* The `$data` argument can be `null`, a valid component string, a object implementing the `__toString` method, an array or a `Traversable` object;
* The `$whence` argument specify the string segment where to include the data;
* The `$whence_index` argument specify the `$whence` index if it is present more than once. The value starts at `0`;
* When using the `remove` method, if the pattern is present multiple times only the first match found is removed 

<p class="message-info"><strong>Tips:</strong> You can easily get the <code>$whence_index</code> by using the object <code>keys($whence)</code> method result.</p>