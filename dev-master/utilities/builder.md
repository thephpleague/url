---
layout: default
title: The URL Formatter
---

# The Builder

The `League\Url\Services\Builder` utility class helps you build and transform as easily as possible a given URL.

The builder is a wrapper class around `League\Url\Url` and all other `League\Url\*` component class to ease URL manipulation.

## Instantiation

To create a new builder object you can provide the `__construct` method with a string:

~~~php
use League\Url\Services\Builder;

$urlBuilder = new Builder('http://www.example.com');
echo $urlBuilder; //display http://www.example.com
~~~

Or a object which exposes the `__toString` method:

~~~php
use League\Url\Url;
use League\Url\Services\Builder;

$urlBuilder = new Builder(Url::createFromUrl('http://www.example.com'));
echo $urlBuilder; //display http://www.example.com
~~~

## Accessing the resulting URL

To access the built URL from the builder object you can:

- use the `Builder::__toString` method
- call the `Builder::getUrl` method which will return a `League\Url\Url` object.

~~~php
use League\Url\Services\Builder;

$urlBuilder = new Builder('http://www.example.com');
echo $urlBuilder; //display http://www.example.com
echo $urlBuilder->getUrl()->getHost(); //display www.example.com
~~~

## The Builder API

While it can sometimes be tedious to directly manipulate a `League\Url\Url` object, the builder exposes simple methods to ease URL manipulation.  
Just like the `League\Url\Url`, the Builder is an immutable value object. So any modification to its internal URL value will return a new instance. This has the side effect of allowing chaining between `Builder` methods.

## Modifying URL query values

The following methods proxy the [Query methods](/dev-master/components/query/) :

### Adding and Updating query values

~~~php
$url = $urlBuilder->mergeQueryValues(['foo' => 'bar'])->getURL();
//use Query::merge() method.
~~~

### Removing query values

~~~php
$url = $urlBuilder->withoutQueryValues(['foo' => 'bar'])->getURL();
//use Query::without() method.
~~~

## Modifying URL path segments

The following methods proxy the [Path methods](/dev-master/components/path/) :

### Appending path segments

~~~php
$url = $urlBuilder->appendPath('/foo/bar')->getURL();
//use Path::append() method.
~~~

### Prepending path segments

~~~php
$url = $urlBuilder->prependPath('/foo/bar')->getURL();
//use Path::append() method.
~~~

### Replacing a path segment

~~~php
$url = $urlBuilder->replacePathSegment('/foo/bar', 0)->getURL();
//use Path::replace() method.
~~~

### Removing path segments

~~~php
$url = $urlBuilder->withoutPathSegments([0, 1])->getURL();
//use Path::without() method.
~~~

### Updating the path extension

~~~php
$url = $urlBuilder->withPathExtension('csv')->getURL();
//use Path::withExtension() method.
~~~

## Modifying URL host labels

The following methods proxy the [Host methods](/dev-master/components/host/) :

### Appending host labels

~~~php
$url = $urlBuilder->appendHost('com')->getURL();
//use Host::append() method.
~~~

### Prepending host labels

~~~php
$url = $urlBuilder->prependHost('shop')->getURL();
//use Host::append() method.
~~~

### Replacing a host label

~~~php
$url = $urlBuilder->replaceHostLabel('thephpleague', 1)->getURL();
//use Host::replace() method.
~~~

### Removing host labels

~~~php
$url = $urlBuilder->withoutHostLabels([0])->getURL();
//use Host::without() method.
~~~