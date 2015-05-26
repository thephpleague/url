---
layout: default
title: Manipulating URL
---

# Modifying URLs

<p class="message-notice">If the modifications does not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## Using the URL components

if your goal is to completely replace one of the URL part you can do so easily using the `Psr\Http\Message\UriInterface` interface modifying methods expose by the object

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

<p class="message-notice">To partially update a URL component you should use the package <a href="/dev-master/services/builder/">builder</a> object instead.</p>

## URL resolution

The URL class also provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is always normalized using all rules even the destructives ones.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$url2 = $url->resolve('./p#~toto');
echo $url2; //displays 'http://www.example.com/hello/p#~toto'
~~~
