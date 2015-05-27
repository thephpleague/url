---
layout: default
title: Manipulating URL
---

# Modifying URLs

<p class="message-notice">If the modifications does not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## Using the URL components

if your goal is to completely replace one of the URL part you can do so easily using the `Psr\Http\Message\UriInterface` interface modifying methods expose by the object

~~~php
use League\Url\Url;

$url = Url::createFromUrl('ftp://thephpleague.com/fr/')
	->withScheme('http')
	->withUserInfo('foo', 'bar')
	->withHost('www.example.com')
	->withPort(81)
	->withPath('/how/are/you')
	->withQuery('foo=baz')
	->withFragment('title');

echo $url; //displays http://foo:bar@www.example.com:81/how/are/you?foo=baz#title
~~~

Since every update returns an instance of `League\Url\Url`, you can chain each setter methods to simplify URL creation and/or modification.

## Partial component modifications

Sometimes what you want to change is not the complete URL component/part but just add, update or remove part of the component. To ease these kind of modification the class comes with various modifying methods to update your URL.

### Modifying URL query parameters

The following methods proxy the [Query modifying methods](/dev-master/components/query/#modifying-a-query) :

#### Adding or Updating query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com//the/sky.php?foo=toto#~typo');
$newUrl = $url->mergeQueryParameters(['foo' => 'bar', 'taz' => '']);
echo $newUrl->getQuery();
//display 'foo=bar&taz'
~~~

uses the same arguments as `League\Url\Query::merge`


#### Removing query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->withoutQueryParameters(['foo']);
echo $newUrl->getQuery();
//display 'p=y%20olo'
~~~

uses the same arguments as `League\Url\Query::without`

#### Filtering query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->filterQueryValues(function ($value) {
	return ! is_array($value);
});
echo $newUrl->getQuery();
//display 'foo=toto&p=y%20olo'
//will update the query string by removing all array-like parameters
~~~

uses the same arguments as `League\Url\Query::filter`

### Modifying URL path segments

The following methods proxy the [Path methods](/dev-master/components/path/#path-normalization) :

#### Appending path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendSegments('/foo/bar');
echo $newUrl->getPath();
//display /path/to/the/sky.php/foo/bar
~~~

uses the same arguments as `League\Url\Path::append`

#### Prepending path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependSegments('/foo/bar');
echo $newUrl->getPath();
//display /foo/bar/path/to/the/sky.php
~~~

uses the same arguments as `League\Url\Path::prepend`

#### Replacing a path segment

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceSegment(0, '/foo/bar');
echo $newUrl->getPath();
//display /foo/bar/to/the/sky.php
~~~

uses the same arguments as `League\Url\Path::replace`

#### Removing path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutSegments([0, 1]);
echo $newUrl->getPath();
//display /the/sky.php
~~~

uses the same arguments as `League\Url\Path::without`

#### Filtering path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->filterSegments(function ($segment) {
	return strpos($segment, 't') === false;
});
echo $newUrl->getPath();
//display /sky.php
~~~

uses the same arguments as `League\Url\Path::filter`

#### Removing dot segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/../to/the/./sky/');
$newUrl = $url->withoutEmptySegments();
echo $newUrl->getPath();
//display /to/the/sky/
~~~

operates like `League\Url\Path::withoutDotSegments`

#### Removing internal empty segments

~~~php
$url = Url::createFromUrl('http://www.example.com///path//to/the////sky//');
$newUrl = $url->withoutEmptySegments();
echo $newUrl->getPath();
//display /path/to/the/sky/
~~~

operates like `League\Url\Path::withoutEmptySegments`

#### Updating the path extension

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withExtension('csv');
echo $newUrl->getPath();
//display /path/to/the/sky.csv
~~~

uses the same arguments as `League\Url\Path::withExtension`

### Modifying URL host labels

The following methods proxy the [Host methods](/dev-master/components/host/#modifying-the-host) :

#### Appending host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendLabels('be');
echo $newUrl->getHost();
//display example.com.be
~~~

uses the same arguments as `League\Url\Host::append`

#### Prepending host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependLabels('shop');
echo $newUrl->getHost();
//display shop.www.example.com
~~~

uses the same arguments as `League\Url\Host::prepend`

#### Replacing a host label

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceLabel(1, 'thephpleague');
echo $newUrl->getHost();
//display shop.www.thephpleague.com
~~~

uses the same arguments as `League\Url\Host::replace`

#### Removing host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutLabels([0]);
echo $newUrl->getHost();
//display example.com
~~~

uses the same arguments as `League\Url\Host::without`

#### Filtering host labels

~~~php
$url = Url::createFromUrl('http://www.eshop.com/path/to/the/sky.php');
$newUrl = $url->filterLabels(function ($label) {
	return strpos($label, 'shop') === false;
});
echo $newUrl->getHost();
//display www.com
//will keep all labels which do not contain the word 'shop'
~~~

uses the same arguments as `League\Url\Host::filter`

## URL resolution

The URL class also provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is always normalized using all rules even the destructives ones.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$newUrl = $url->resolve('./p#~toto');
echo $newUrl; //displays 'http://www.example.com/hello/p#~toto'
~~~
