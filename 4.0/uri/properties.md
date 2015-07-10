---
layout: default
title: Getting URIs informations
---

# Extracting data from URIs

An URI is composed of several parts and components. the `Uri` object was built to expose as much information as possible to ease URI manipulation.

## Accessing URI parts and components

### URI as an array

You can get the URI as an `array` similar to `parse_url` response if you call `Uri::toArray` method. The only difference being that the returned array contains all 8 components. When the component is not set its value is `null`.

~~~php
use League\Uri\Uri;

$url = Uri::createFromString("http://www.example.com/how/are/you?foo=baz");
var_export($url->toArray());
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
use League\Uri\Uri;

$url = Uri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz#title");
echo $url->getScheme();    //displays "http"
echo $url->getUserInfo();  //displays "foo:bar"
echo $url->getHost();      //displays "www.example.com"
echo $url->getPort();      //displays 81 as an integer
echo $url->getAuthority(); //displays "foo:bar@www.example.com:81"
echo $url->getPath();      //displays "/how/are/you"
echo $url->getQuery();     //displays "foo=baz"
echo $url->getFragment();  //displays "title"
~~~

### Parts and components as objects

To access a specific URI part or component as an object you can use PHP"s magic method `__get` as follow.

~~~php
use League\Uri\Uri;

$url = Uri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz#title");
$url->scheme;         //return a League\Uri\Scheme object
$url->userInfo;       //return a League\Uri\UserInfo object
$url->host;           //return a League\Uri\Host object
$url->port;           //return a League\Uri\Port object
$url->path;           //return a League\Uri\Path object
$url->query;          //return a League\Uri\Query object
$url->fragment;       //return a League\Uri\Fragment object
$url->schemeRegistry; //return a League\Uri\Scheme\Registry object
~~~

Using this technique you can get even more informations regarding your URI.

~~~php
use League\Uri\Uri;

$url = Uri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz");
$url->host->isIp();           //return false the URI uses a registered hostname
$url->userInfo->getUser();    //return "foo" the user login information
$url->fragment->isEmpty();    //return true because to fragment component is empty
$url->path->getBasename();    //return "you"
$url->query->getValue("foo"); //return "baz"
~~~

To get more informations about component properties refer to the [components documentation](/4.0/components/overview/)

## URI properties

### Is the URI empty ?

An URI can have a empty string representation even if some components or URI parts are not.

~~~php
use League\Uri\Uri;

$url = Uri::createFromString("//example.com:82");
$url->getPort(); //return 82
$url->getHost(); //return "example.com"
$url->isEmpty(); //return false

$newUrl = $url->withHost("");
$newUrl->getPort(); //return 82
$newUrl->getHost(); //return ""
$newUrl->isEmpty(); //return true
~~~

### Does the URI uses the standard port ?

If the standard port defined for a specific scheme is used it will be remove:

- from the URI string;
- from the array representations;

The `Uri::hasStandardPort` tells you whether you are using or not the standard port for a given scheme.

- If **no scheme** is set, the method returns `false`.
- If **no port** is set the method will return `true`.

~~~php
use League\Uri\Uri;

$url = Uri::createFromString("http://example.com:8042/over/there");
$url->hasStandardPort(); //return false
echo $url->getPort();    //displays 8042
echo $url;               //displays "http://example.com:8042/over/there"

$alt_url = Uri::createFromString("wss://example.com:443/over/there");
$alt_url->hasStandardPort(); //return true
echo $alt_url->getPort();    //displays 443
echo $alt_url;               //displays "wss://example.com/over/there"
~~~

### Does URIs refers to the same resource/location

You can compare two PSR-7 `UriInterface` compliant URIs object to see if they represent the same resource using the `Uri::sameValueAs` method. The method compares the two objects according to their respective `__toString` methods with the following normalizations applied before comparison:

- the query string is sorted according to their parameters keys;

~~~php
use League\Uri\Uri;
use GuzzleHttp\Psr7\Uri;

$leagueUrl = Uri::createFromString("http://www.рф.ru:80/hello/world?foo=bar&baz=yellow");
$guzzleUrl = new Uri("http://www.xn--p1ai.ru:80/hello/world?baz=yellow&foo=bar");

$leagueUrl->sameValueAs($guzzleUrl); //return true
~~~