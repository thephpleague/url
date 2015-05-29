---
layout: default
title: Manipulating URL
---

# Modifying URLs

<p class="message-notice">If the modifications does not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## URL normalization

Out of the box the package normalize any given URL according to the non destructive rules of RFC3986.

These non destructives rules are:

- scheme and host components are lowercased;
- query, path, fragment components are URL encoded;
- the port number is removed from the URL object if the standard port is used;
- if no host is present, the user information part and the port are removed from the URL object;

~~~php
use League\Url\Url;

$url = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
echo $url; //displays http://www.example.com/hellow/./wor%20ld?who=f%203#title
~~~

## URL resolution

The URL class provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is normalized according to RFC3986 rules.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$newUrl = $url->resolve('./p#~toto');
echo $newUrl; //displays 'http://www.example.com/hello/p#~toto'
~~~

## Complete URL components and parts modifications

To completely replace one of the URL part you can do so easily using the `Psr\Http\Message\UriInterface` interface modifying methods expose by the object

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

## Partial URL components modifications

Often what you really want is to partially update the URL component. Using the current public API it is possible but requires boilerplate code. To ease these operations various modifying methods where added. Each method is presented independently but keep in mind that:

- The [facade pattern](http://en.wikipedia.org/wiki/Facade_pattern
) is used to integrate specific components modifying methods. They accept the same arguments and work the same but instead of returning a specific component object, their return a `League\Url\Url` object.

- You can chain them as they all return an instance of `League\Url\Url`.

- You can get more informations on how the method works by following the link to the method referenced.

### Modifying URL query parameters

#### Add or Update query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com//the/sky.php?foo=toto#~typo');
$newUrl = $url->mergeQueryParameters(['foo' => 'bar', 'taz' => '']);
echo $newUrl->getQuery();
//display 'foo=bar&taz'
~~~

`Url::mergeQueryParameters` is a facade to [League\Url\Query::merge](/dev-master/components/query/#add-or-update-parameters).

#### Remove query parameters

~~~php
$url = Url::createFromUrl('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->withoutQueryParameters(['foo']);
echo $newUrl->getQuery();
//display 'p=y%20olo'
~~~

`Url::withoutQueryParameters` is a facade to [League\Url\Query::without](/dev-master/components/query/#remove-parameters).

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

`Url::filterQuery` is a facade to [League\Url\Query::filter](/dev-master/components/query/#filter-the-query).

### Modifying URL path segments

#### Append path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendPath('/foo/bar');
echo $newUrl->getPath();
//display /path/to/the/sky.php/foo/bar
~~~

`Url::appendPath` is a facade to [League\Url\Path::append](/dev-master/components/path/#append-segments).

#### Prepend path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependPath('/foo/bar');
echo $newUrl->getPath();
//display /foo/bar/path/to/the/sky.php
~~~

`Url::prependPath` is a facade to [League\Url\Path::prepend](/dev-master/components/path/#prepend-segments).

#### Replace a path segment

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceSegment(0, '/foo/bar');
echo $newUrl->getPath();
//display /foo/bar/to/the/sky.php
~~~

`Url::replaceSegment` is a facade to [League\Url\Path::replace](/dev-master/components/path/#replace-segments).

#### Remove path segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutSegments([0, 1]);
echo $newUrl->getPath();
//display /the/sky.php
~~~

`Url::withoutSegments` is a facade to [League\Url\Path::without](/dev-master/components/path/#remove-segments).

#### Filter the path

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->filterPath(function ($segment) {
	return strpos($segment, 't') === false;
});
echo $newUrl->getPath();
//display /sky.php
~~~

`Url::filterPath` is a facade to [League\Url\Path::filter](/dev-master/components/path/#filter-segments).

#### Remove dot segments

~~~php
$url = Url::createFromUrl('http://www.example.com/path/../to/the/./sky/');
$newUrl = $url->withoutDotSegments();
echo $newUrl->getPath();
//display /to/the/sky/
~~~

`Url::withoutDotSegments` is a facade to [League\Url\Path::withoutDotSegments](/dev-master/components/path/#removing-dot-segments).

#### Remove internal empty segments

~~~php
$url = Url::createFromUrl('http://www.example.com///path//to/the////sky//');
$newUrl = $url->withoutEmptySegments();
echo $newUrl->getPath();
//display /path/to/the/sky/
~~~

`Url::withoutEmptySegments` is a facade to [League\Url\Path::withoutEmptySegments](/dev-master/components/path/#removing-empty-segments).

#### Update the path extension

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withExtension('csv');
echo $newUrl->getPath();
//display /path/to/the/sky.csv
~~~

`Url::withExtension` is a facade to [League\Url\Path::withExtension](/dev-master/components/path/#path-extension-manipulation).

### Modifying URL host labels

#### Append host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendHost('be');
echo $newUrl->getHost();
//display example.com.be
~~~

`Url::appendHost` is a facade to [League\Url\Host::append](/dev-master/components/host/#append-labels).

#### Prepend host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependHost('shop');
echo $newUrl->getHost();
//display shop.www.example.com
~~~

`Url::prependHost` is a facade to [League\Url\Host::prepend](/dev-master/components/host/#prepend-labels).

#### Replace a host label

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceLabel(1, 'thephpleague');
echo $newUrl->getHost();
//display www.thephpleague.com
~~~

`Url::replaceLabel` is a facade to [League\Url\Host::replace](/dev-master/components/host/#replace-label).

#### Remove host labels

~~~php
$url = Url::createFromUrl('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutLabels([0]);
echo $newUrl->getHost();
//display example.com
~~~

`Url::withoutLabels` is a facade to [League\Url\Host::without](/dev-master/components/host/#remove-labels).

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

`Url::filterHost` is a facade to [League\Url\Host::filter](/dev-master/components/host/#filter-labels).
