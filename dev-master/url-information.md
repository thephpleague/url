---
layout: default
title: Getting URLs information
---

# The URL object

## Manipulating URL components

If you want to create or update quickly an URL, then you'll need to use `League\Url\Url` object which implements the PSR-7 interface.

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

Since every update returns a modified instance, you can chain each setter methods to simplify URL creation and/or modification.

<p class="message-notice">If the modifications does not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## Getting access to URL components

### URL components

You can access the URL individual components using their respective getter methods. All components are object implementing the `League\Url\Interfaces\Component` interface. This interface provide a `__toString` method to help you get a quick access to its string representation.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
echo $url->getScheme();    //displays 'http'
echo $url->getUser();      //displays 'foo'
echo $url->getPass();      //displays 'bar'
echo $url->getHost();      //displays 'www.example.com'
echo $url->getPort();      //displays '81'
echo $url->getPath();      //displays '/how/are/you'
echo $url->getQuery();     //displays 'foo=baz'
echo $url->getFragment();  //displays 'title'
~~~

You can also get the same information as an `array` similar to `parse_url` response if you call `Url::toArray` method. The only difference being that the returned array contains all 8 components even when they are not net.

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

### URL parts

Sometimes you may want to get the RFC3986 parts of the URLs. To do so two additionals methods are provided:

~~~php
use League\Url\Url;

$url = Url::createFromUrl('http://foo:bar@www.example.com:81/how/are/you?foo=baz#title');
echo $url->getAuthority(); //displays 'foo:bar@www.example.com:81'
echo $url->getUserInfo();  //displays 'foo:bar'
~~~

## URL properties

### Is the URL absolute ?

An URL is considered absolute if it has a non empty scheme component.

~~~php
use League\Url\Url;

$url = Url:createFromUrl('//example.com/foo');
$url->isAbsolute(); //returns false

$url = Url:createFromUrl('ftp:://example.com/foo');
$url->isAbsolute(); //returns true
~~~

### Does the URL uses the standard port ?

If the standard port defined for a specific scheme is used it will be dropped from the string representation of the URL. The `Url::hasStandardPort` tells you whether you are using or not the standard port for a given scheme.

- If the scheme is not known, the method returns `false`.
- If no port is set, the method will return `true`.

~~~php
use League\Url\Url;

Url::createFromUrl('http://example.com:8042/over/there'')->hasStandardPort(); // returns false
Url::createFromUrl('wss://example.com:443/over/there'')->hasStandardPort(); // returns true
~~~

## URL normalization

Out of the box the package normalize the given URL according to the non destructive rules of RFC3986.

The non destructives rules are:

- scheme and host components are lowercased;
- query, path, fragment components are URL encoded;
- the port number is stripped from the URL output if the standard port is used;

~~~php
use League\Url\Url;

$url = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
echo $url; //displays http://www.example.com/hellow/./wor%20ld?who=f%203#title
~~~

If you wish to remove the dot segments which is considered a destructive normalization you will have to explicitly call the `Url::normalize` method which takes no argument.

~~~php
use League\Url\Url;

$url    = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$newUrl = $url->->normalize();
echo $newUrl; //displays http://www.example.com/hellow/wor%20ld?who=f%203#title
~~~

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object will be returned.</p>

## URL comparison

To compare two URLs to know if they represent the same ressource you can use the `Url::sameValueAs` method which compares two PSR-7 `UriInterface` object according to their respective `UriInterface::__toString` methods.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=I+am');
$url2 = Url::createFromUrl('http://www.example.com/hellow/./wor%2Fld?who=I%2Dam;');
$url3 = $url2->normalize();

$url1->sameValueAs($url2); //return true
$url1->samaValueAs($url3); //return false;
~~~

## URL resolution

The URL class also provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is always normalized using all rules even the destructives ones.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$url2 = $url->resolve('./p#~toto');
echo $url2; //displays 'http://www.example.com/hello/p#~toto'
~~~