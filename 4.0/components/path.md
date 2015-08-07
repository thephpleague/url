---
layout: default
title: The Path component
---

# The Path component

The library provides a basic `League\Uri\Components\Path` class to ease path manipulation.

## Path creation

### Using the default constructor

Just like any other component, a new `League\Uri\Components\HierarchicalPath` object can be instantiated using its default constructor.

~~~php
use League\Uri\Components\Path as Path;

$path = new Path('/hello/world');
echo $path; //display '/hello/world'

$altPath = new Path('text/plain;charset=us-ascii,Hello%20World%21');
echo $altPath; //display 'text/plain;charset=us-ascii,Hello%20World%21'
~~~

<p class="message-warning">If the submitted value is not a valid path an <code>InvalidArgumentException</code> will be thrown.</p>

## Path representations

### String representation

Basic path representations is done using the following methods:

~~~php
use League\Uri\Components\Path as Path;

$path = new Path('/path/to the/sky');
$path->__toString();      //return '/path/to%20the/sky'
$path->getUriComponent(); //return '/path/to%20the/sky'
~~~

## Path normalization

<p class="message-notice">If the modifications do not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> exception is thrown.</p>

Out of the box, the `HierarchicalPath` object operates a number of non destructive normalizations. For instance, the path is correctly URI encoded against the RFC3986 rules.

### Removing dot segments

To remove dot segment as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-6) you need to explicitly call the `Path::withoutDotSegments` method as the result can be destructive. The method takes no argument and returns a new `Path` object which represents the current object without dot segments.

~~~php
use League\Uri\Components\Path as Path;

$raw_path       = new Path('path/to/./the/../the/sky%7bfoo%7d');
$normalize_path = $raw_path->withoutDotSegments();
echo $raw_path;           //displays 'path/to/./the/../the/sky%7bfoo%7d'
echo $normalize_path;     //displays 'path/to/the/sky%7Bfoo%7D'
$alt->sameValueAs($path); //return false;
~~~

<p class="message-notice">This method is used by the URI Object <code>withoutDotSegments</code> method</p>

## Specialized Path Object

What makes a URI difference apart from the scheme is how the path is parse and manipulated. This simple path class although functional will not help you parse or manipulate correctly a Data URI path or a FTP Uri path. That's why the library comes bundles with two specialized Path objects:

- the [HierarchicalPath](/4.0/components/hierarchical-path/) object to work with HTTP, FTP, WS paths component
- the [DataPath](/4.0/components/datauri-path/) object to work with the DataURI path
- the [Extension Guide](/4.0/uri/extension/) also provides examples on how to extends the Path object to make it meets you specific URI parsing and manipulation methods.
