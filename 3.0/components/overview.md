---
layout: default
title: URL Components
---

# URL components

An URL string is composed of up to 8 components. The `League\Url` library provides interfaces and classes to interact with each URL component. The classes can all be use independently of a `League\Url\UrlInterface` implementing class.

## Component Interface

Each component class implements the `League\Url\Components\ComponentInterface` with the following public methods:


### ComponentInterface::set($data)

Sets the component value.

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;

### ComponentInterface::get()

Returns `null` if the class data is empty or its string representation

### ComponentInterface::__toString()

Returns a typecast string representation of the component.

### ComponentInterface::getUriComponent()

Returns an altered string representation to ease URL representation.


### ComponentInterface::sameValueAs(ComponentInterface $component)

<p class="message-notice">Added in <code>version 3.2</code></p>

Return `true` if both components string representation values are equals.

<h2 id="simple-components">Single Value Components</h2>

The URL components classes which represent single values only:

* implement the `League\Url\Components\ComponentInterface` interface.
* differ in the way they validate and/or output the components.

These classes are:

* `League\Url\Components\Scheme` for the scheme component;
* `League\Url\Components\User` for the user component;
* `League\Url\Components\Pass` for the pass component;
* `League\Url\Components\Port` for the port component;
* `League\Url\Components\Fragment` for the fragment component;

Example using the `League\Url\Components\Scheme` class:

~~~php
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

###  ComponentArrayInterface::toArray()

Returns an array representation of the component;

###  ComponentArrayInterface::keys()

Returns all the keys or a subset of the keys of an array if a value is given.

<p class="message-info"><strong>Of note:</strong> The <code>$data</code> argument for the <code>set</code> method can also be an <code>array</code> or a <code>Traversable</code> object.</p>

The URL components classes implementing these interfaces are:

* [League\Url\Components\Query](/components/query/) for the query component;
* [League\Url\Components\Path](/components/path/) for the path component;
* [League\Url\Components\Host](/components/host/) for the host component;

<h2 id="segment-components">Segment Values Components</h2>

[League\Url\Components\Path](/components/path/) and 
[League\Url\Components\Host](/components/host/) also implement the `League\Url\Components\SegmentInterface` interface which adds the following methods:

The `$data` argument used in all described method below can be `null`, a valid component string, a object implementing the `__toString` method, an array or a `Traversable` object;

### SegmentInterface::append($data, $whence = null, $whence_index = null)

Appends data into the component.

* The `$whence` argument specify the string segment where to include the data;
* The `$whence_index` argument specify the `$whence` index if it is present more than once. The value starts at `0`;

### SegmentInterface::prepend($data, $whence = null, $whence_index = null)

Prepends data into the component;

* The `$whence` argument specify the string segment where to include the data;
* The `$whence_index` argument specify the `$whence` index if it is present more than once. The value starts at `0`;

<p class="message-info"><strong>Tips:</strong> You can easily get the <code>$whence_index</code> by using the object <code>keys($whence)</code> method result.</p>

### SegmentInterface::remove($data)

Removes data from the component. If the pattern is present multiple times only the first match found is removed.
