---
layout: default
title: Manipulating URL
---

# Modifying URLs

<p class="message-notice">If the modifications does not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

## Using the URL components

If you want to create or update quickly an URL, then you'll need to use `League\Url\Url` object which implements the PSR-7 `Psr\Http\Message\UriInterface` interface.

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
$newUrl = $url->normalize();
echo $newUrl; //displays http://www.example.com/hellow/wor%20ld?who=f%203#title
~~~

## URL resolution

The URL class also provides the mean for resolving an URL as a browser would for an anchor tag. When performing URL resolution the returned URL is always normalized using all rules even the destructives ones.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl('hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title');
$url2 = $url->resolve('./p#~toto');
echo $url2; //displays 'http://www.example.com/hello/p#~toto'
~~~

