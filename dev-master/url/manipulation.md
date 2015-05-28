---
layout: default
title: Manipulating URL
---

# Modifying URLs

<p class="message-notice">If the modifications does not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## URL resolution

The URL class provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is always normalized using all rules even the destructives ones.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$newUrl = $url->resolve('./p#~toto');
echo $newUrl; //displays 'http://www.example.com/hello/p#~toto'
~~~

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

## Partial modifications

Often what you want is not to update the complete URL component/part but just add, update or remove some of their content. To ease partial modifications the class comes with various modifying methods to update your URL. Each method is presented independently but keep in mind that you can chain them as they all return an instance of `League\Url\Url`.

### Modifying URL query parameters

The following methods proxy the [Query modifying methods](/dev-master/components/query/#modifying-a-query) :

#### Add or Update query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com//the/sky.php?foo=toto#~typo');
$newUrl = $url->mergeQueryParameters(['foo' => 'bar', 'taz' => '']);
echo $newUrl->getQuery();
//display 'foo=bar&taz'
~~~

uses the same arguments as [League\Url\Query::merge](/dev-master/components/query/#add-or-update-parameters)

#### Remove query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->withoutQueryParameters(['foo']);
echo $newUrl->getQuery();
//display 'p=y%20olo'
~~~

uses the same arguments as [League\Url\Query::without](/dev-master/components/query/#remove-parameters)

#### Filter query

~~~php
$url = Url::createFromUrl('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->filterQuery(function ($value) {
	return ! is_array($value);
});
echo $newUrl->getQuery();
//display 'foo=toto&p=y%20olo'
//will update the query string by removing all array-like parameters
~~~

uses the same arguments as [League\Url\Query::filter](/dev-master/components/query/#filter-the-query)

### Modifying URL path segments

The following methods proxy the [Path methods](/dev-master/components/path/#path-normalization) :

#### Append path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendSegments('/foo/bar');
echo $newUrl->getPath();
//display /path/to/the/sky.php/foo/bar
~~~

uses the same arguments as [League\Url\Path::append](/dev-master/components/path/#append-segments)

#### Prepend path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependSegments('/foo/bar');
echo $newUrl->getPath();
//display /foo/bar/path/to/the/sky.php
~~~

uses the same arguments as [League\Url\Path::prepend](/dev-master/components/path/#prepend-segments)

#### Replace a path segment

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceSegment(0, '/foo/bar');
echo $newUrl->getPath();
//display /foo/bar/to/the/sky.php
~~~

uses the same arguments as [League\Url\Path::replace](/dev-master/components/path/#replace-segments)

#### Remove path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutSegments([0, 1]);
echo $newUrl->getPath();
//display /the/sky.php
~~~

uses the same arguments as [League\Url\Path::without](/dev-master/components/path/#remove-segments)

#### Filter the path

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->filterPath(function ($segment) {
	return strpos($segment, 't') === false;
});
echo $newUrl->getPath();
//display /sky.php
~~~

uses the same arguments as [League\Url\Path::filter](/dev-master/components/path/#filter-segments)

#### Remove dot segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/../to/the/./sky/');
$newUrl = $url->withoutDotSegments();
echo $newUrl->getPath();
//display /to/the/sky/
~~~

operates like [League\Url\Path::withoutDotSegments](/dev-master/components/path/#removing-dot-segments)

#### Remove internal empty segments

~~~php
$url = Url::createFromUrl('http://www.example.com///path//to/the////sky//');
$newUrl = $url->withoutEmptySegments();
echo $newUrl->getPath();
//display /path/to/the/sky/
~~~

operates like [League\Url\Path::withoutEmptySegments](/dev-master/components/path/#removing-empty-segments)

#### Update the path extension

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withExtension('csv');
echo $newUrl->getPath();
//display /path/to/the/sky.csv
~~~

uses the same arguments as [League\Url\Path::withExtension](/dev-master/components/path/#path-extension-manipulation)

### Modifying URL host labels

The following methods proxy the [Host methods](/dev-master/components/host/#modifying-the-host) :

#### Append host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendLabels('be');
echo $newUrl->getHost();
//display example.com.be
~~~

uses the same arguments as [League\Url\Host::append](/dev-master/components/host/#append-labels)

#### Prepend host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependLabels('shop');
echo $newUrl->getHost();
//display shop.www.example.com
~~~

uses the same arguments as [League\Url\Host::prepend](/dev-master/components/host/#prepend-labels)

#### Replace a host label

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceLabel(1, 'thephpleague');
echo $newUrl->getHost();
//display www.thephpleague.com
~~~

uses the same arguments as [League\Url\Host::replace](/dev-master/components/host/#replace-label)

#### Remove host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutLabels([0]);
echo $newUrl->getHost();
//display example.com
~~~

uses the same arguments as [League\Url\Host::without](/dev-master/components/host/#remove-labels)

#### Filter the host

~~~php
$url = Url::createFromUrl('http://www.eshop.com/path/to/the/sky.php');
$newUrl = $url->filterHost(function ($label) {
	return strpos($label, 'shop') === false;
});
echo $newUrl->getHost();
//display www.com
//will keep all labels which do not contain the word 'shop'
~~~

uses the same arguments as [League\Url\Host::filter](/dev-master/components/host/#filter-labels)
