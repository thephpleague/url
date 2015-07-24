---
layout: default
title: Getting URIs informations
---

# Extracting data from URIs

An URI is composed of several parts and components. the `Uri` object was built to expose as much information as possible to ease URI manipulation.

<p class="message-notice">The methods and properties describes are available on all URI objects unless explicitly expressed.</p>

## Accessing URI parts and components

### URI as an array

You can get the URI as an `array` similar to `parse_url` response if you call `Uri::toArray` method. The only difference being that the returned array contains all 8 components. When the component is not set its value is `null`.

~~~php
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString("http://www.example.com/how/are/you?foo=baz");
var_export($uri->toArray());
//return the following array
// array (
//  "scheme" => "http",
//  "user" => NULL,
//  "pass" => NULL,
//  "host" => "www.example.com",
//  "port" => NULL,
//  "path" => "/how/are/you",
//  "query" => "foo=baz",
//  "fragment" => NULL,
// );
~~~

### Parts and components as strings

You can access the URI individual parts and components as string or integer using their respective getter methods.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz#title");
echo $uri->getScheme();    //displays "http"
echo $uri->getUserInfo();  //displays "foo:bar"
echo $uri->getHost();      //displays "www.example.com"
echo $uri->getPort();      //displays 81 as an integer
echo $uri->getAuthority(); //displays "foo:bar@www.example.com:81"
echo $uri->getPath();      //displays "/how/are/you"
echo $uri->getQuery();     //displays "foo=baz"
echo $uri->getFragment();  //displays "title"
~~~

### Parts and components as objects

To access a specific URI part or component as an object you can use PHP"s magic method `__get` as follow.

~~~php
use League\Uri\Schemes\Ws as WsUri;

$uri = WsUri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz");
$uri->scheme;   //return a League\Uri\Scheme object
$uri->userInfo; //return a League\Uri\UserInfo object
$uri->host;     //return a League\Uri\Host object
$uri->port;     //return a League\Uri\Port object
$uri->path;     //return a League\Uri\Path object
$uri->query;    //return a League\Uri\Query object
$uri->fragment; //return a League\Uri\Fragment object
~~~

Using this technique you can get even more informations regarding your URI.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz");
$uri->host->isIp();           //return false the URI uses a registered hostname
$uri->userInfo->getUser();    //return "foo" the user login information
$uri->fragment->isEmpty();    //return true because to fragment component is empty
$uri->path->getBasename();    //return "you"
$uri->query->getValue("foo"); //return "baz"
~~~

To get more informations about component properties refer to the [components documentation](/4.0/components/overview/)

## URI properties

### Is the URI empty ?

An URI can have a empty string representation even if some components or URI parts are not. The emptyness of a URI is scheme dependent.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("//example.com:82");
$uri->getPort(); //return 82
$uri->getHost(); //return "example.com"
$uri->isEmpty(); //return false

$newUri = $uri->withHost("");
$newUri->getPort(); //return 82
$newUri->getHost(); //return ""
$newUri->isEmpty(); //return true
~~~

### Does the URI uses the standard port ?

If the standard port defined for a specific scheme is used it will be remove:

- from the URI string;
- from the array representations;

The `Uri::hasStandardPort` tells you whether you are using or not the standard port for a given scheme.

- If **no scheme** is set, the method returns `false`.
- If **no port** is set the method will return `true`.

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Ws as WsUri;

$uri = HttpUri::createFromString("http://example.com:8042/over/there");
$uri->hasStandardPort(); //return false
echo $uri->getPort();    //displays 8042
echo $uri;               //displays "http://example.com:8042/over/there"

$alt_uri = WsUri::createFromString("wss://example.com:443/over/there");
$alt_uri->hasStandardPort(); //return true
echo $alt_uri->getPort();    //displays 443
echo $alt_uri;               //displays "wss://example.com/over/there"
~~~

### Does URIs refers to the same resource/location

#### League URI objects

You can compare two URI object to see if they represent the same resource using the `sameValueAs` method. The method compares the two objects according to their respective `__toString` methods with the following normalizations applied before comparison:

- the host is converted using the punycode algorithm;
- the path is normalized according to RFC3986 rules;
- the query string is sorted according to their parameters keys;

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Ftp as FtpUri;

$httpUri = HttpUri::createFromString("http://www.рф.ru:/hello/world?foo=bar&baz=yellow");
$ftpUri  = FtpUri::createFromString("ftp://www.xn--p1ai.Ru:80/hello/world?baz=yellow&foo=bar");

$httpUri->sameValueAs($ftpUri); //return false
~~~

#### PSR-7 UriInterface objects

To allow more interoperability, you can also compare a PSR-7 `UriInterface` compliant URIs object with a League URI object. The same normalization are applied.

~~~php
use League\Uri\Schemes\Http as HttpUri;
use GuzzleHttp\Psr7\Uri;

$leagueUri = HttpUri::createFromString("http://www.рф.ru:/hello/world?foo=bar&baz=yellow");
$psr7Uri = new Uri("http://www.xn--p1ai.Ru:80/hello/world?baz=yellow&foo=bar");

$leagueUri->sameValueAs($psr7Uri); //return true
~~~