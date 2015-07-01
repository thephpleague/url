---
layout: default
title: Getting URLs informations
---

# Extracting data from URLs

An URL is composed of several parts:

~~~
foo://example.com:8042/over/there?name=ferret#nose
\_/   \______________/\_________/ \_________/ \__/
 |           |            |            |        |
scheme   authority       path        query   fragment
~~~

The URL authority part in itself can be composed of up to 3 parts.

~~~
john:doe@example.com:8042
\______/ \_________/ \__/
    |         |        |
userinfo    host     port
~~~

## Accessing URL parts and components

### URL as an array

You can get the URL as an `array` similar to `parse_url` response if you call `Url::toArray` method. The only difference being that the returned array contains all 8 components. When the component is not set its value is `null`.

~~~php
use League\Uri\Url;

$url = Url::createFromString('http://www.example.com/how/are/you?foo=baz');
var_export($url->toArray());
//return the following array
// array (
//  'scheme' => 'http',
//  'user' => NULL,
//  'pass' => NULL,
//  'host' => 'www.example.com',
//  'port' => NULL,
//  'path' => '/how/are/you',
//  'query' => 'foo=baz',
//  'fragment' => NULL,
// );
~~~

### Parts and components as strings

You can access the URL individual parts and components as string or integer using their respective getter methods.

~~~php
use League\Uri\Url;

$url = Url::createFromString('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
echo $url->getScheme();    //displays 'http'
echo $url->getUserInfo();  //displays 'foo:bar'
echo $url->getHost();      //displays 'www.example.com'
echo $url->getPort();      //displays 81 as an integer
echo $url->getAuthority(); //displays 'foo:bar@www.example.com:81'
echo $url->getPath();      //displays '/how/are/you'
echo $url->getQuery();     //displays 'foo=baz'
echo $url->getFragment();  //displays 'title'
~~~

### Parts and components as objects

To access a specific URL part or component as an object you can use the magic method `__get` as follow.

~~~php
use League\Uri\Url;

$url = Url::createFromString('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
$url->scheme;   //return a League\Uri\Scheme object
$url->userInfo; //return a League\Uri\UserInfo object
$url->host;     //return a League\Uri\Host object
$url->port;     //return a League\Uri\Port object
$url->path;     //return a League\Uri\Path object
$url->query;    //return a League\Uri\Query object
$url->fragment; //return a League\Uri\Fragment object
~~~

Using this technique you can get even more informations regarding a URL.

~~~php
use League\Uri\Url;

$url = Url::createFromString('http://foo:bar@www.example.com:81/how/are/you?foo=baz');
$url->host->isIp();           //return false the URL uses a registered hostname
$url->fragment->isEmpty();    //return true because to fragment component is empty
$url->path->getBasename();    //return 'you'
$url->query->getValue('foo'); //return 'baz'
~~~

To get more informations about component properties refer to the [components documentation](/4.0/components/overview/)

## URL properties

### Is the URL empty ?

An URL can have a empty string representation even if some components or URL parts are not.

~~~php
use League\Uri\Url;

$url = Url:createFromString('//example.com:82');
$url->getPort(); //return 82
$url->getHost(); //return 'example.com'
$url->isEmpty(); //return false

$newUrl = $url->withHost('');
$newUrl->getPort(); //return 82
$newUrl->getHost(); //return ''
$newUrl->isEmpty(); //return true
~~~

### Is the URL absolute ?

An URL is considered absolute if it has a non empty scheme component and an authority part.

~~~php
use League\Uri\Url;

$url = Url:createFromString('//example.com/foo');
$url->isAbsolute(); //return false

$url = Url:createFromString('ftp://example.com/foo');
$url->isAbsolute(); //return true
~~~

### Does the URL uses the standard port ?

If the standard port defined for a specific scheme is used it will be remove from the URL string or array representations. The `Url::hasStandardPort` tells you whether you are using or not the standard port for a given scheme.

- If **no scheme** is set, the method returns `false`.
- If **no port** is set the method will return `true`.

~~~php
use League\Uri\Url;

$url = Url::createFromString('http://example.com:8042/over/there');
$url->hasStandardPort(); //return false
echo $url->getPort();    //displays 8042
echo $url;               //displays 'http://example.com:8042/over/there'

$alt_url = Url::createFromString('wss://example.com:443/over/there');
$alt_url->hasStandardPort(); //return true
echo $alt_url->getPort();    //displays 443
echo $alt_url;               //displays 'wss://example.com/over/there'
~~~

### Does URLs refers to the same resource/location

You can compare two PSR-7 `UriInterface` compliant URLs object to see if they represent the same resource using the `Url::sameValueAs` method. The method compares the two objects according to their respective `__toString` methods with the following normalizations applied before comparison:

- each host is converted using the punycode algorithm;
- each query string is sorted according to their offsets;

~~~php
use League\Uri\Url;
use GuzzleHttp\Psr7\Uri;

$leagueUrl = Url::createFromString('http://www.рф.ru:80/hello/world?foo=bar&baz=yellow');
$guzzleUrl = new Uri('http://www.рф.ru:80/hello/world?baz=yellow&foo=bar');

$leagueUrl->sameValueAs($guzzleUrl); // return true
~~~