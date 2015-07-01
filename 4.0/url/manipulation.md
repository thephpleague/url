---
layout: default
title: Manipulating URL
---

# Modifying URLs

<p class="message-notice">If the modifications do not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## URL normalization

Out of the box the package normalizes any given URL according to the non destructive rules of RFC3986.

These non destructives rules are:

- scheme and host components are lowercased;
- host component is encoded using the punycode algorithm if needed
- query, path, fragment components are URL encoded;
- the port number is removed from the URL string representation if the standard port is used;

~~~php
use League\Uri\Url;

$url = Url::createFromString('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
echo $url; //displays http://www.example.com/hellow/./wor%20ld?who=f%203#title
~~~

## URL resolution

The URL class provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is normalized according to RFC3986 rules.

~~~php
use League\Uri\Url;

$url = Url::createFromString('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$newUrl = $url->resolve('./p#~toto');
echo $newUrl; //displays 'http://www.example.com/hello/p#~toto'
~~~

## Complete URL components and parts modifications

To completely replace one of the URL part you can use the `Psr\Http\Message\UriInterface` interface modifying methods exposed by the object

~~~php
use League\Uri\Url;

$url = Url::createFromString('ftp://thephpleague.com/fr/')
    ->withScheme('http')
    ->withUserInfo('foo', 'bar')
    ->withHost('www.example.com')
    ->withPort(81)
    ->withPath('/how/are/you')
    ->withQuery('foo=baz')
    ->withFragment('title');

echo $url; //displays http://foo:bar@www.example.com:81/how/are/you?foo=baz#title
~~~

Since every update returns an instance of `League\Uri\Url`, you can chain each setter methods to simplify URL creation and/or modification.

## Partial URL components modifications

Often what you really want is to partially update one of the URL component. Using the current public API it is possible but requires several intermediary steps. For instance here's how you would update the query string from a given URL object:

~~~php
$url         = Url::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$urlQuery    = $url->query;
$updateQuery = $urlQuery->merge(['foo' => 'bar', 'taz' => '']);
$newUrl      = $url->withQuery($updateQuery->__toString());
echo $newUrl; // display http://www.example.com/the/sky.php?foo=bar&taz#~typo
~~~

To ease these operations various modifying methods were added. Each method is presented independently but keep in mind that:

- They return a `League\Uri\Url` object. So you can chain them to simplify URL manipulation.
- Their arguments are always proxied to a specific component modifying methods.
- You can get more informations on how the method works by following the link to the method proxied.

### Modifying URL query parameters

### Sort query parameters

~~~php
$url = Url::createFromString("http://www.example.com/the/sky.php?yellow=tiger&browser=lynx");
$newUrl = $url->ksortQuery();
echo $newUrl; //display 'http://www.example.com/the/sky.php?browser=lynx&yellow=tiger'
~~~

`Url::ksortQuery` is a facade to simplify the use of [League\Uri\Query::ksort](/4.0/components/query/#sort-parameters).

#### Add or Update query parameters

~~~php
$url = Url::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$newUrl = $url->mergeQuery(['foo' => 'bar', 'taz' => '']);
echo $newUrl; //display 'http://www.example.com/the/sky.php?foo=bar&taz#~typo'
~~~

`Url::mergeQuery` is a facade to simplify the use of [League\Uri\Query::merge](/4.0/components/query/#add-or-update-parameters).

#### Remove query values

~~~php
$url = Url::createFromString('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->withoutQueryValues(['foo']);
echo $newUrl; //display 'http://www.example.com/the/sky.php?p=y%20olo#~typo'
~~~

`Url::withoutQueryValues` is a facade to simplify the use of [League\Uri\Query::without](/4.0/components/query/#remove-parameters).

#### Filter query

~~~php
$url = Url::createFromString('http://www.example.com/to/sky.php?foo[]=toto&foo[]=bar&p=y+olo#~typo');
$newUrl = $url->filterQuery(function ($value) {
    return ! is_array($value);
});
echo $newUrl; //display 'http://www.example.com/the/sky.php?p=y%20olo#~typo'
//will update the query string by removing all array-like parameters
~~~

`Url::filterQuery` is a facade to simplify the use of [League\Uri\Query::filter](/4.0/components/query/#filter-the-query).

### Modifying URL path segments

#### Append path segments

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendPath('/foo/bar');
echo $newUrl; //display 'http://www.example.com/path/to/the/sky.php/foo/bar'
~~~

`Url::appendPath` is a facade to simplify the use of [League\Uri\Path::append](/4.0/components/path/#append-segments).

#### Prepend path segments

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependPath('/foo/bar');
echo $newUrl; //display 'http://www.example.com/foo/bar/path/to/the/sky.php'
~~~

`Url::prependPath` is a facade to simplify the use of [League\Uri\Path::prepend](/4.0/components/path/#prepend-segments).

#### Replace a path segment

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceSegment(0, '/foo/bar');
echo $newUrl; //display 'http://www.example.com/foo/bar/to/the/sky.php'
~~~

`Url::replaceSegment` is a facade to simplify the use of [League\Uri\Path::replace](/4.0/components/path/#replace-segments).

#### Remove path segments

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutSegments([0, 1]);
echo $newUrl; //display 'http://www.example.com/the/sky.php'
~~~

`Url::withoutSegments` is a facade to simplify the use of [League\Uri\Path::without](/4.0/components/path/#remove-segments).

#### Filter the path

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->filterPath(function ($segment) {
    return strpos($segment, 't') === false;
});
echo $newUrl; //display 'http://www.example.com/sky.php'
~~~

`Url::filterPath` is a facade to simplify the use of [League\Uri\Path::filter](/4.0/components/path/#filter-segments).

#### Remove dot segments

~~~php
$url = Url::createFromString('http://www.example.com/path/../to/the/./sky/');
$newUrl = $url->withoutDotSegments();
echo $newUrl; //display 'http://www.example.com/to/the/sky/'
~~~

`Url::withoutDotSegments` is a facade to simplify the use of [League\Uri\Path::withoutDotSegments](/4.0/components/path/#removing-dot-segments).

#### Remove internal empty segments

~~~php
$url = Url::createFromString('http://www.example.com///path//to/the////sky//');
$newUrl = $url->withoutEmptySegments();
echo $newUrl; //display 'http://www.example.com/path/to/the/sky/'
~~~

`Url::withoutEmptySegments` is a facade to simplify the use of [League\Uri\Path::withoutEmptySegments](/4.0/components/path/#removing-empty-segments).

#### Update the path extension

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withExtension('csv');
echo $newUrl; //display 'http://www.example.com/path/to/the/sky.csv'
~~~

`Url::withExtension` is a facade to simplify the use of [League\Uri\Path::withExtension](/4.0/components/path/#path-extension-manipulation).

### Modifying URL host labels

#### Append host labels

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendHost('be');
echo $newUrl; //display 'http://www.example.com.be/path/to/the/sky.php'
~~~

`Url::appendHost` is a facade to simplify the use of [League\Uri\Host::append](/4.0/components/host/#append-labels).

#### Prepend host labels

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependHost('shop');
echo $newUrl; //display 'http://shop.www.example.com/path/to/the/sky.php'
~~~

`Url::prependHost` is a facade to simplify the use of [League\Uri\Host::prepend](/4.0/components/host/#prepend-labels).

#### Replace a host label

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceLabel(1, 'thephpleague');
echo $newUrl; //display 'http://www.thephpleague.com/path/to/the/sky.php'
~~~

`Url::replaceLabel` is a facade to simplify the use of [League\Uri\Host::replace](/4.0/components/host/#replace-label).

#### Remove host labels

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutLabels([0]);
echo $newUrl; //display 'http://example.com/path/to/the/sky.php'
~~~

`Url::withoutLabels` is a facade to simplify the use of [League\Uri\Host::without](/4.0/components/host/#remove-labels).

### Remove the host zone identifier

~~~php
$url = Url::createFromString('http://[fe80::1%25eth0-1]/path/to/the/sky.php');
$newUrl = $url->withoutZoneIdentifier();
echo $newUrl; //display 'http://[fe80::1]/path/to/the/sky.php'
~~~

`Url::withoutZoneIdentifier` is a facade to simplify the use of [League\Uri\Host::withoutZoneIdentifier](/4.0/components/host/#remove-zone-identifier).

#### Filter the host

~~~php
$url = Url::createFromString('http://www.eshop.com/path/to/the/sky.php');
$newUrl = $url->filterHost(function ($label) {
    return strpos($label, 'shop') === false;
});
echo $newUrl; //display 'http://www.com/path/to/the/sky.php'
//will keep all labels which do not contain the word 'shop'
~~~

`Url::filterHost` is a facade to simplify the use of [League\Uri\Host::filter](/4.0/components/host/#filter-labels).
