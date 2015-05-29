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
use League\Url\Url;

$url = Url::createFromUrl('http://www.example.com/how/are/you?foo=baz');
$url->toArray();
//returns the following array
//    [
//        'scheme' => 'http',
//        'user' => null,
//        'pass' => null,
//        'host' => 'example.com',
//        'port' => null,
//        'path' => '/how/are/you',
//        'query' => 'foo=baz',
//        'fragment' => null,
//    ];
~~~

### Parts and components as strings

You can access the URL individual parts and components as string or integer using their respective getter methods.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
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
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
$url->scheme;   //returns a League\Url\Scheme object
$url->userInfo; //returns a League\Url\UserInfo object
$url->host;     //returns a League\Url\Host object
$url->port;     //returns a League\Url\Port object
$url->path;     //returns a League\Url\Path object
$url->query;    //returns a League\Url\Query object
$url->fragment; //returns a League\Url\Fragment object
~~~

Using this technique you can get even more informations regarding a URL.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz');
$url->host->isIp();        //returns false the URL uses a registered hostname
$url->fragment->isEmpty(); //returns true because to fragment component is empty
$url->path->getBasename(); //returns 'you';
~~~

To get more informations about component properties refer to the [components documentation](/dev-master/components/overview/)

## URL properties

### Is the URL absolute ?

An URL is considered absolute if it has a non empty scheme component and an authority part.

~~~php
use League\Url\Url;

$url = Url:createFromUrl('//example.com/foo');
$url->isAbsolute(); //returns false

$url = Url:createFromUrl('ftp://example.com/foo');
$url->isAbsolute(); //returns true
~~~

### Does the URL uses the standard port ?

If the standard port defined for a specific scheme is used it will be remove from the URL object and any of its representation. The `Url::hasStandardPort` tells you whether you are using or not the standard port for a given scheme.

- If **no scheme** is set, the method returns `false`.
- If **no port** is set the method will return `true`.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://example.com:8042/over/there');
$url->hasStandardPort(); //returns false
echo $url->getPort();    //displays 8042;

$alt_url = Url::createFromUrl('wss://example.com:443/over/there');
$alt_url->hasStandardPort(); // returns true
echo $url->getPort();        //displays null; the Port number is automatically dropped
~~~

### Does URLs refers to the same resource/location

You can compare two PSR-7 `UriInterface` compliant URLs object to see if they represent the same resource using the `Url::sameValueAs` method.

This method compares the two objects according to their respective `__toString` methods response.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('http://www.example.com:80/hello/world');
$url2 = Url::createFromUrl('http://www.example.com/hello/world');

$url1->sameValueAs($url2); //return true
~~~
