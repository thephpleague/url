---
layout: default
title: URLs as Value Objects
permalink: overview/
---

# Overview

The library handles FTP, HTTP and Websocket protocol URLs through the use of two main classes:

* `League\Url\Url` a value object that represents a URL
* `League\Url\UrlImmutable` a immutable value object that represents a URL

Both classes implement the `League\Url\UrlInterface` interface.

Think of PHP `DateTime` and `DateTimeImmutable` classes which implement the `DateTimeInterface` interface.

<p class="message-warning">While the library validate the host syntax it does not validate your host against a valid <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## Instantiation

Both classes share the same named constructors to ease object instantiation. In the example below I'll use the `League\Url\Url` object as an example but the same is true for `League\Url\UrlImmutable`.

~~~php
<?php

use League\Url\Url;

//Method 1 : from a given URLs
$url = Url::createFromUrl('ftp://host.example.com');

//Method 2: from the current PHP page
//don't forget to provide the $_SERVER array
$url = Url::createFromServer($_SERVER); 
~~~

`$url` is a `League\Url\Url` object

## Outputting the Urls

The `League\Url\UrlInterface` interface provide the following methods:

* `__toString` returns the full string representation of the URL;
* `getUserInfo` returns the string representation of the URL user info;
* `getAuthority` returns the string representation of the URL authority part (ie: `user`, `pass`, `host`, `port`);
* `getBaseUrl` returns the string representation of the URL `scheme` component and authority part;
* `getRelativeUrl(UrlInterface $ref_url = null)` returns the string representation of the URL relative to another `League\Url\UrlInterface` object;
* `sameValueAs` returns `true` if two `League\Url\UrlInterface` object represents the same URL.

**The following was added in version 3.2:**

* `getUserInfo`;
* `getAuthority`;
* the `$ref_url` argument was added to `getRelativeUrl` method;

<p class="message-info">On URL output, the query string is automatically encoded following the <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a></p>

~~~php
use League\Url\Url;
use League\Url\UrlImmutable;

$url = Url::createFromUrl('http://www.example.com/path/index.php?query=toto+le+heros');
$relative_url = Url::createFromUrl('http://www.example.com/path/another/index.html');
echo $url; // 'http://www.example.com/path/index.php?query=toto%20le%20heros'
echo $url->getBaseUrl(); // http://www.example.com
echo $url->getRelativeUrl(); // /path/index.php?query=toto%20le%20heros
echo $url->getRelativeUrl($relative_url); // ../../index.php?query=toto%20le%20heros

$original_url = Url::createFromUrl("example.com"); //a schemeless url
$new_url = UrlImmutable::createFromUrl("//example.com"); //another schemeless url
$alternate_url = Url::createFromUrl("http://example.com");

$original_url->sameValueAs($new_url); //will return true
$original_url->sameValueAs($alternate_url); //will return false
~~~

## Manipulating Urls

An URL string is composed of up to 8 components. For each object, each URL component can be access and modify through its own setter and getter method.

* Chaining is possible since all the setter methods return a `League\Url\UrlInterface` object;
* Getter methods return a [League\Url\Component\ComponentInterface][basic] object;

Here's a complete list of all the setter and getter provided by the `League\Url\UrlInterface` interface:

* `setScheme($data)` set the URL scheme component;
* `getScheme()` returns a [League\Url\Components\ComponentInterface][basic] object
* `setUser($data)` set the URL user component;
* `getUser()` returns a [League\Url\Components\ComponentInterface][basic] object
* `setPass($data)` set the URL pass component;
* `getPass()` returns a [League\Url\Components\ComponentInterface][basic] object
* `setHost($data)` set the URL host component;
* `getHost()` returns a [League\Url\Components\HostInterface](/components/host/) object
* `setPort($data)` set the URL port component;
* `getPort()` returns a [League\Url\Components\ComponentInterface][basic] object
* `setPath($data)` set the URL path component;
* `getPath()` returns a [League\Url\Components\PathInterface](/components/path/) object
* `setQuery($data)` set the URL query component;
* `getQuery()` returns a [League\Url\Components\QueryInterface](/components/query/) object
* `setFragment($data)` set the URL fragment component;
* `getFragment()` returns a [League\Url\Components\ComponentInterface][basic]`object

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;
* another specific component object;
* for `setHost`, `setPath`, `setQuery`: an `array` or a `Traversable` object;

Let's modify a `League\Url\Url` object 

~~~php
$url = Url::createFromUrl('https://www.example.com');
$url
	->setUser('john')
	->setPass('doe')
	->setPort(443)
	->setScheme('https');
echo $url; // https://john:doe@www.example.com:443/

$port = $url->getPort();
$port->set(80);
echo $port; // output 80;
echo $url; // https://john:doe@www.example.com:80/
~~~

<div class="message-warning">
To stay immutable, the <code>League\Url\UrlImmutable</code> object:
<ul>
<li>never modified itself but return a new object instead.</li>
<li>returns a new property object instead of its own property object to avoid modification by reference.</li>
</ul>
</div>

The same operation using a <code>League\Url\UrlImmutable</code> object:

~~~php
$url = UrlImmutable::createFromUrl('http://www.example.com');
$new_url = $url
	->setUser('john')
	->setPass('doe')
	->setPort(443)
	->setScheme('https');
echo $url; //remains http://www.example.com/
echo $new_url; //output https://john:doe@www.example.com:443/

$port = $new_url->getPort(); //$port is a clone object of the URL port component.
echo $port // output 443;
$port->set(80);
echo $port; // output 80;
echo $new_url->getPort(); //remains 443
~~~

[basic]: /components/overview/#simple-components