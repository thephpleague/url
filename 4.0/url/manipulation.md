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

- They all return a `League\Uri\Url` object. So you can chain them to simplify URL manipulation.
- Their arguments are always proxied to a specific component modifying methods.
- You can get more informations on how the method works by following the link to the method proxied.

### Modifying URL query parameters

#### Sort query parameters

~~~php
$url    = Url::createFromString("http://www.example.com/the/sky.php?yellow=tiger&browser=lynx");
$newUrl = $url->ksortQuery();
echo $newUrl; //display 'http://www.example.com/the/sky.php?browser=lynx&yellow=tiger'
~~~

`Url::ksortQuery` is a proxy to simplify the use of [League\Uri\Query::ksort](/4.0/components/query/#sort-parameters) on a `Url` object.

#### Add or Update query parameters

~~~php
$url = Url::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$newUrl = $url->mergeQuery(['foo' => 'bar', 'taz' => '']);
echo $newUrl; //display 'http://www.example.com/the/sky.php?foo=bar&taz#~typo'
~~~

`Url::mergeQuery` is a proxy to simplify the use of [League\Uri\Query::merge](/4.0/components/query/#add-or-update-parameters) on a `Url` object.

#### Remove query values

~~~php
$url = Url::createFromString('http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo');
$newUrl = $url->withoutQueryValues(['foo']);
echo $newUrl; //display 'http://www.example.com/the/sky.php?p=y%20olo#~typo'
~~~

`Url::withoutQueryValues` is a proxy to simplify the use of [League\Uri\Query::without](/4.0/components/query/#remove-parameters) on a `Url` object.

#### Filter query

~~~php
$url = Url::createFromString('http://www.example.com/to/sky.php?foo[]=toto&foo[]=bar&p=y+olo#~typo');
$newUrl = $url->filterQuery(function ($value) {
    return ! is_array($value);
});
echo $newUrl; //display 'http://www.example.com/the/sky.php?p=y%20olo#~typo'
//will update the query string by removing all array-like parameters
~~~

`Url::filterQuery` is a proxy to simplify the use of [League\Uri\Query::filter](/4.0/components/query/#filter-the-query) on a `Url` object.

### Modifying URL path segments

#### Append path segments

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendPath('/foo/bar');
echo $newUrl; //display 'http://www.example.com/path/to/the/sky.php/foo/bar'
~~~

`Url::appendPath` is a proxy to simplify the use of [League\Uri\Path::append](/4.0/components/path/#append-segments) on a `Url` object.

#### Prepend path segments

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependPath('/foo/bar');
echo $newUrl; //display 'http://www.example.com/foo/bar/path/to/the/sky.php'
~~~

`Url::prependPath` is a proxy to simplify the use of [League\Uri\Path::prepend](/4.0/components/path/#prepend-segments) on a `Url` object.

#### Replace a path segment

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceSegment(0, '/foo/bar');
echo $newUrl; //display 'http://www.example.com/foo/bar/to/the/sky.php'
~~~

`Url::replaceSegment` is a proxy to simplify the use of [League\Uri\Path::replace](/4.0/components/path/#replace-segments) on a `Url` object.

#### Remove path segments

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutSegments([0, 1]);
echo $newUrl; //display 'http://www.example.com/the/sky.php'
~~~

`Url::withoutSegments` is a proxy to simplify the use of [League\Uri\Path::without](/4.0/components/path/#remove-segments) on a `Url` object.

#### Filter the path

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->filterPath(function ($segment) {
    return strpos($segment, 't') === false;
});
echo $newUrl; //display 'http://www.example.com/sky.php'
~~~

`Url::filterPath` is a proxy to simplify the use of [League\Uri\Path::filter](/4.0/components/path/#filter-segments) on a `Url` object.

#### Remove dot segments

~~~php
$url = Url::createFromString('http://www.example.com/path/../to/the/./sky/');
$newUrl = $url->withoutDotSegments();
echo $newUrl; //display 'http://www.example.com/to/the/sky/'
~~~

`Url::withoutDotSegments` is a proxy to simplify the use of [League\Uri\Path::withoutDotSegments](/4.0/components/path/#removing-dot-segments) on a `Url` object.

#### Remove internal empty segments

~~~php
$url = Url::createFromString('http://www.example.com///path//to/the////sky//');
$newUrl = $url->withoutEmptySegments();
echo $newUrl; //display 'http://www.example.com/path/to/the/sky/'
~~~

`Url::withoutEmptySegments` is a proxy to simplify the use of [League\Uri\Path::withoutEmptySegments](/4.0/components/path/#removing-empty-segments) on a `Url` object.

#### Update the path extension

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withExtension('csv');
echo $newUrl; //display 'http://www.example.com/path/to/the/sky.csv'
~~~

`Url::withExtension` is a proxy to simplify the use of [League\Uri\Path::withExtension](/4.0/components/path/#path-extension-manipulation) on a `Url` object.

#### Add trailing slash

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withTrailingSlash();
echo $newUrl; //display 'http://www.example.com/path/to/the/sky.php/'
~~~

`Url::withTrailingSlash` is a proxy to simplify the use of [League\Uri\Path::withTrailingSlash](/4.0/components/path/#path-trailing-slash-manipulation) on a `Url` object.

#### Remove trailing slash

~~~php
$url = Url::createFromString('http://www.example.com/');
$newUrl = $url->withoutTrailingSlash();
echo $newUrl; //display 'http://www.example.com'
~~~

`Url::withoutTrailingSlash` is a proxy to simplify the use of [League\Uri\Path::withoutTrailingSlash](/4.0/components/path/#path-trailing-slash-manipulation) on a `Url` object.

### Modifying URL host labels

#### Append host labels

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->appendHost('be');
echo $newUrl; //display 'http://www.example.com.be/path/to/the/sky.php'
~~~

`Url::appendHost` is a proxy to simplify the use of [League\Uri\Host::append](/4.0/components/host/#append-labels) on a `Url` object.

#### Prepend host labels

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->prependHost('shop');
echo $newUrl; //display 'http://shop.www.example.com/path/to/the/sky.php'
~~~

`Url::prependHost` is a proxy to simplify the use of [League\Uri\Host::prepend](/4.0/components/host/#prepend-labels) on a `Url` object.

#### Replace a host label

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->replaceLabel(1, 'thephpleague');
echo $newUrl; //display 'http://www.thephpleague.com/path/to/the/sky.php'
~~~

`Url::replaceLabel` is a proxy to simplify the use of [League\Uri\Host::replace](/4.0/components/host/#replace-label) on a `Url` object.

#### Remove host labels

~~~php
$url = Url::createFromString('http://www.example.com/path/to/the/sky.php');
$newUrl = $url->withoutLabels([0]);
echo $newUrl; //display 'http://example.com/path/to/the/sky.php'
~~~

`Url::withoutLabels` is a proxy to simplify the use of [League\Uri\Host::without](/4.0/components/host/#remove-labels) on a `Url` object.

### Remove the host zone identifier

~~~php
$url = Url::createFromString('http://[fe80::1%25eth0-1]/path/to/the/sky.php');
$newUrl = $url->withoutZoneIdentifier();
echo $newUrl; //display 'http://[fe80::1]/path/to/the/sky.php'
~~~

`Url::withoutZoneIdentifier` is a proxy to simplify the use of [League\Uri\Host::withoutZoneIdentifier](/4.0/components/host/#remove-zone-identifier) on a `Url` object.

#### Convert to IDN host

~~~php
$url    = Url::createFromString('http://xn--p1ai.ru/path/to/the/sky.php');
$newUrl = $url->toUnicode();
echo $newUrl; //display 'http://рф.ru/path/to/the/sky.php'
~~~

`Url::toUnicode` is a proxy to simplify the use of [League\Uri\Host::toUnicode](/4.0/components/host/#transcode-the-host) on a `Url` object.

#### Convert to Ascii host

~~~php
$url    = Url::createFromString('http://рф.ru/path/to/the/sky.php');
$newUrl = $url->toAscii();
echo $newUrl; //display 'http://xn--p1ai.ru/path/to/the/sky.php'
~~~

`Url::toAscii` is a proxy to simplify the use of [League\Uri\Host::toAscii](/4.0/components/host/#transcode-the-host) on a `Url` object.

#### Filter the host

~~~php
$url = Url::createFromString('http://www.eshop.com/path/to/the/sky.php');
$newUrl = $url->filterHost(function ($label) {
    return strpos($label, 'shop') === false;
});
echo $newUrl; //display 'http://www.com/path/to/the/sky.php'
//will keep all labels which do not contain the word 'shop'
~~~

`Url::filterHost` is a proxy to simplify the use of [League\Uri\Host::filter](/4.0/components/host/#filter-labels) on a `Url` object.
