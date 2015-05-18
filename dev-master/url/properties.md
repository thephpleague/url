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

The URL authority part in itself can be composed of up to 4 components.

~~~
john:doe@example.com:8042
\_/  \_/ \_________/ \__/
 |    |       |        |
user pass   host     port
~~~

To be able to access all these parts, the `League\Url\Url` class exposes the following public API:

## URL components

You can access the URL individual components using their respective getter methods.

All returned components are objects implementing the `League\Url\Interfaces\UrlPart` interface. [This interface](/dev-master/component/) provide a `__toString` method to help you get a quick access to its string representation.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
echo $url->getScheme();    //displays 'http'
echo $url->getUserInfo();  //displays 'foo:bar'
echo $url->getHost();      //displays 'www.example.com'
echo $url->getPort();      //displays '81'
echo $url->getPath();      //displays '/how/are/you'
echo $url->getQuery();     //displays 'foo=baz'
echo $url->getFragment();  //displays 'title'
~~~

<p class="message-notice">In order to access the URL credentials, you are required to proxy your call throught the <code>getUserInfo</code> method</p>

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
echo $url->getUserInfo();             //displays 'foo:bar'
echo $url->getUserInfo()->getUser();  //displays 'foo'
echo $url->getUserInfo()->getPass();  //displays 'bar'
~~~

You can also get the same information as an `array` similar to `parse_url` response if you call `Url::toArray` method. The only difference being that the returned array contains all 8 components. When the component is not set its value is `null`.

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

## URL parts

To comply with RFC3986 one additional method is provided to returns the URL [authority](http://tools.ietf.org/html/rfc3986#section-3.2) part. `Url::getAuthority` returns a `string`.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
echo $url->getAuthority(); //displays 'foo:bar@www.example.com:81'
~~~

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

If the standard port defined for a specific scheme is used it will be dropped from the string representation of the URL. The `Url::hasStandardPort` tells you whether you are using or not the standard port for a given scheme.

- If the scheme is unknown by the library, the method returns `false`.
- If **no port** is set the method will return `true`.

~~~php
use League\Url\Url;

Url::createFromUrl('http://example.com:8042/over/there')->hasStandardPort(); // returns false
Url::createFromUrl('wss://example.com:443/over/there')->hasStandardPort(); // returns true
~~~

### Does URLs refers to the same resource/location

You can compare two PSR-7 compliant URLs object to see if they represent the same resource using the `Url::sameValueAs` method.

This method compares the two objects according to their respective `__toString` methods response.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('http://www.example.com:80/hello/world');
$url2 = Url::createFromUrl('http://www.example.com/hello/world');

$url1->sameValueAs($url2); //return true
~~~
