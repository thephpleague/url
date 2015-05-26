---
layout: default
title: The URL Formatter
---

# The Builder

The Builder service provides a convenient, fluent interface to create and manipulate URL. It can be used to perform most URL parts operations in your application.

All modifying methods proxy methods attached to URL parts or components corresponding classes. To get a better understanding on how they work and the parameters they need a link to the corresponding URL part API is provided.

## Instantiation

To create a new builder object you can simply create a new instance  as shown below:

~~~php
use League\Url\Services\Builder;

$urlBuilder = new Builder('http://www.example.com');
~~~

The constructor accepts a string or an object which exposes the `__toString` method like any PSR-7 `UriInterface` compliant object:

~~~php
use League\Url\Url;
use League\Url\Services\Builder;

$urlBuilder = new Builder(Url::createFromServer($_SERVER));
~~~

## Accessing the resulting URL

As the name implied the builder role is only to build an URL. To get access the built URL you must call the `Builder::getUrl` method which returns a `League\Url\Url` object.

~~~php
use League\Url\Services\Builder;

$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php?foo=bar#~typo');
$url = $urlBuilder->getUrl(); //$url is a League\Url\Url object
echo $url; //display 'http://www.example.com/path/to/the/sky.php?foo=bar#~typo'
echo $url->getHost(); //display www.example.com
~~~

## Modifying URL query parameters

The following methods proxy the [Query modifying methods](/dev-master/components/query/#modifying-a-query) :

### Adding or Updating query parameters

~~~php
$urlBuilder = new Builder('http://www.example.com//the/sky.php?foo=toto#~typo');
echo $urlBuilder->mergeQueryParameters(['foo' => 'bar', 'taz' => ''])->getURL()->getQuery();
//display 'foo=bar&taz'
~~~

uses the same arguments as `League\Url\Query::merge`


### Removing query parameters

~~~php
$urlBuilder = new Builder('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
echo $urlBuilder->withoutQueryParameters(['foo'])->getURL()->getQuery();
//display 'p=y%20olo'
~~~

uses the same arguments as `League\Url\Query::without`

### Filtering query parameters

~~~php
$urlBuilder = new Builder('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
echo $urlBuilder->filterQueryValues(function ($value) {
	return ! is_array($value);
})->getURL()->getQuery();
//display 'foo=toto&p=y%20olo'
//will update the query string by removing all array-like parameters
~~~

uses the same arguments as `League\Url\Query::filter`

## Modifying URL path segments

The following methods proxy the [Path methods](/dev-master/components/path/#path-normalization) :

### Appending path segments

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->appendSegments('/foo/bar')->getURL()->getPath();
//display /path/to/the/sky.php/foo/bar
~~~

uses the same arguments as `League\Url\Path::append`

### Prepending path segments

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->prependSegments('/foo/bar')->getURL()->getPath();
//display /foo/bar/path/to/the/sky.php
~~~

uses the same arguments as `League\Url\Path::prepend`

### Replacing a path segment

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->replaceSegment(0, '/foo/bar')->getURL()->getPath();
//display /foo/bar/to/the/sky.php
~~~

uses the same arguments as `League\Url\Path::replace`

### Removing path segments

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->withoutSegments([0, 1])->getURL()->getPath();
//display /the/sky.php
~~~

uses the same arguments as `League\Url\Path::without`

### Filtering path segments

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->filterSegments(function ($segment) {
	return strpos($segment, 't') === false;
})->getURL()->getPath();
//display /sky.php
~~~

uses the same arguments as `League\Url\Path::filter`

### Removing dot segments

~~~php
$urlBuilder = new Builder('http://www.example.com/path/../to/the/./sky/');
echo $urlBuilder->withoutEmptySegments()->getURL()->getPath();
//display /to/the/sky/
~~~

operates like `League\Url\Path::withoutDotSegments`

### Removing internal empty segments

~~~php
$urlBuilder = new Builder('http://www.example.com///path//to/the////sky//');
echo $urlBuilder->withoutEmptySegments()->getURL()->getPath();
//display /path/to/the/sky/
~~~

operates like `League\Url\Path::withoutEmptySegments`

### Updating the path extension

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->withExtension('csv')->getURL()->getPath();
//display /path/to/the/sky.csv
~~~

uses the same arguments as `League\Url\Path::withExtension`

## Modifying URL host labels

The following methods proxy the [Host methods](/dev-master/components/host/#modifying-the-host) :

### Appending host labels

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->appendLabels('be')->getURL()->getHost();
//display example.com.be
~~~

uses the same arguments as `League\Url\Host::append`

### Prepending host labels

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->prependLabels('shop')->getURL()->getHost();
//display shop.www.example.com
~~~

uses the same arguments as `League\Url\Host::prepend`


### Replacing a host label

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->replaceLabel(1, 'thephpleague')->getURL()->getHost();
//display shop.www.thephpleague.com
~~~

uses the same arguments as `League\Url\Host::replace`


### Removing host labels

~~~php
$urlBuilder = new Builder('http://www.example.com/path/to/the/sky.php');
echo $urlBuilder->withoutLabels([0])->getURL()->getHost();
//display example.com
~~~

uses the same arguments as `League\Url\Host::without`


### Filtering host labels

~~~php
$urlBuilder = new Builder('http://www.eshop.com/path/to/the/sky.php');
echo $urlBuilder->filterLabels(function ($label) {
	return strpos($label, 'shop') === false;
})->getURL()->getHost();
//display www.com
//will keep all labels which do not contain the word 'shop'
~~~

uses the same arguments as `League\Url\Host::filter`

