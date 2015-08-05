---
layout: default
title: Manipulating URI
---

# Modifying URIs

<p class="message-notice">If the modifications do not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">The method may throw an <code>InvalidArgumentException</code> if the resulting URI is not valid for a scheme specific URI.</p>

## URI normalization

Out of the box the package normalizes any given URI according to the non destructive rules of RFC3986.

These non destructives rules are:

- scheme and host components are lowercased;
- query, path, fragment components are URI encoded;
- the port number is removed from the URI string representation if the standard port is used;

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
echo $uri; //displays http://www.example.com/hellow/./wor%20ld?who=f%203#title
~~~

## Resolving a relative URI

The URI class provides the mean for resolving an URI as a browser would for an anchor tag. When performing URI resolution the returned URI is normalized according to RFC3986 rules. The uri to resolved must be another `Uri` object.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$newUri = $uri->resolve(HttpUri::createFromString("./p#~toto"));
echo $newUri; //displays "http://www.example.com/hello/p#~toto"
~~~

<p class="message-notice">If you try to resolve two Uri object which do not share the same scheme. No normalization will occur and the submitted URI object will be return unchanged.</p>

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;

$uri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$newUri = $uri->resolve(WsUri::createFromString("./p#~toto"));
echo $newUri; //displays "./p#~toto"
~~~

## Complete components and parts modifications

To completely replace one of the URI part you can use the `Psr\Http\Message\UriInterface` interface modifying methods exposed by the object

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("ftp://thephpleague.com/fr/")
    ->withScheme("http")
    ->withUserInfo("foo", "bar")
    ->withHost("www.example.com")
    ->withPort(81)
    ->withPath("/how/are/you")
    ->withQuery("foo=baz")
    ->withFragment("title");

echo $uri; //displays http://foo:bar@www.example.com:81/how/are/you?foo=baz#title
~~~

Since every update returns an instance of `League\Uri\Schemes\Generic\AbstractHierarchical`, you can chain each setter methods to simplify URI creation and/or modification.

## Partial components modifications

Often what you really want is to partially update one of the URI component. Using the current public API it is possible but requires several intermediary steps. For instance here"s how you would update the query string from a given URI object:

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri         = HttpUri::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$uriQuery    = $uri->query;
$updateQuery = $uriQuery->merge(["foo" => "bar", "taz" => ""]);
$newUri      = $uri->withQuery($updateQuery->__toString());
echo $newUri; // display http://www.example.com/the/sky.php?foo=bar&taz#~typo
~~~

To ease these operations various modifying methods were added. Each method is presented independently but keep in mind that:

- They all return a `League\Uri\Schemes\Generic\AbstractHierarchical` object. So you can chain them to simplify URI manipulation.
- Their arguments are always proxied to a specific component modifying methods.
- You can get more informations on how the method works by following the link to the method proxied.

### Modifying URI path

#### Remove dot segments

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/../to/the/./sky/");
$newUri = $uri->withoutDotSegments();
echo $newUri; //display "http://www.example.com/to/the/sky/"
~~~

`Uri::withoutDotSegments` is a proxy to simplify the use of [HierarchicalPath::withoutDotSegments](/4.0/components/hierarchical-path/#removing-dot-segments) on a `Uri` object.

### Modifying URI query parameters

#### Sort query parameters

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/the/sky.php?yellow=tiger&browser=lynx");
$newUri = $uri->ksortQuery();
echo $newUri; //display "http://www.example.com/the/sky.php?browser=lynx&yellow=tiger"
~~~

`Uri::ksortQuery` is a proxy to simplify the use of [Query::ksort](/4.0/components/query/#sort-parameters) on a `Uri` object.

#### Add or Update query parameters

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$newUri = $uri->mergeQuery(["foo" => "bar", "taz" => ""]);
echo $newUri; //display "http://www.example.com/the/sky.php?foo=bar&taz#~typo"
~~~

`Uri::mergeQuery` is a proxy to simplify the use of [Query::merge](/4.0/components/query/#add-or-update-parameters) on a `Uri` object.

#### Remove query values

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/to/sky.php?foo=toto&p=y+olo#~typo");
$newUri = $uri->withoutQueryValues(["foo"]);
echo $newUri; //display "http://www.example.com/the/sky.php?p=y%20olo#~typo"
~~~

`Uri::withoutQueryValues` is a proxy to simplify the use of [Query::without](/4.0/components/query/#remove-parameters) on a `Uri` object.

#### Filter query

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("//example.com/to/sky.php?foo[]=toto&foo[]=bar&p=y+olo#~typo");
$newUri = $uri->filterQuery(function ($value) {
    return ! is_array($value);
});
echo $newUri; //display "//example.com/the/sky.php?p=y%20olo#~typo"
//will update the query string by removing all array-like parameters
~~~

`Uri::filterQuery` is a proxy to simplify the use of [Query::filter](/4.0/components/query/#filter-the-query) on a `Uri` object.

### Modifying URI host labels

#### Append host labels

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->appendHost("be");
echo $newUri; //display "http://www.example.com.be/path/to/the/sky.php"
~~~

`Uri::appendHost` is a proxy to simplify the use of [Host::append](/4.0/components/host/#append-labels) on a `Uri` object.

#### Prepend host labels

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->prependHost("shop");
echo $newUri; //display "http://shop.www.example.com/path/to/the/sky.php"
~~~

`Uri::prependHost` is a proxy to simplify the use of [Host::prepend](/4.0/components/host/#prepend-labels) on a `Uri` object.

#### Replace a host label

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->replaceLabel(1, "thephpleague");
echo $newUri; //display "http://www.thephpleague.com/path/to/the/sky.php"
~~~

`Uri::replaceLabel` is a proxy to simplify the use of [Host::replace](/4.0/components/host/#replace-label) on a `Uri` object.

#### Remove host labels

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->withoutLabels([0]);
echo $newUri; //display "http://example.com/path/to/the/sky.php"
~~~

`Uri::withoutLabels` is a proxy to simplify the use of [Host::without](/4.0/components/host/#remove-labels) on a `Uri` object.

#### Remove the host zone identifier

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://[fe80::1%25eth0-1]/path/to/the/sky.php");
$newUri = $uri->withoutZoneIdentifier();
echo $newUri; //display "http://[fe80::1]/path/to/the/sky.php"
~~~

`Uri::withoutZoneIdentifier` is a proxy to simplify the use of [Host::withoutZoneIdentifier](/4.0/components/host/#remove-zone-identifier) on a `Uri` object.

#### Convert to IDN host

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri    = HttpUri::createFromString("http://xn--p1ai.ru/path/to/the/sky.php");
$newUri = $uri->toUnicode();
echo $newUri; //display "http://рф.ru/path/to/the/sky.php"
~~~

`Uri::toUnicode` is a proxy to simplify the use of [Host::toUnicode](/4.0/components/host/#transcode-the-host) on a `Uri` object.

#### Convert to Ascii host

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri    = HttpUri::createFromString("http://рф.ru/path/to/the/sky.php");
$newUri = $uri->toAscii();
echo $newUri; //display "http://xn--p1ai.ru/path/to/the/sky.php"
~~~

`Uri::toAscii` is a proxy to simplify the use of [Host::toAscii](/4.0/components/host/#transcode-the-host) on a `Uri` object.

#### Filter the host

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.eshop.com/path/to/the/sky.php");
$newUri = $uri->filterHost(function ($label) {
    return strpos($label, "shop") === false;
});
echo $newUri; //display "http://www.com/path/to/the/sky.php"
//will keep all labels which do not contain the word "shop"
~~~

`Uri::filterHost` is a proxy to simplify the use of [Host::filter](/4.0/components/host/#filter-labels) on a `Uri` object.
