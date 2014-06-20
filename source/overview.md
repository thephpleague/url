---
layout: layout
title: URLs as Value Objects
---

# Overview

The library is composed of two main classes:

* `League\Url\Url` a value object that represents an URL
* `League\Url\UrlImmutable` a immutable value object that represents an URL

## Instantiation

Both classes share the same named constructors to ease object instantiation. In the example below I'll use the `League\Url\Url` object as an example but the same is true for `League\Url\UrlImmutable`.

~~~.language-php
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

Both classes implement the `League\Url\UrlInterface` interface (think of PHP `DateTime` and `DateTimeImmutable` classes which implement the `DateTimeInterface`) 

The `League\Url\UrlInterface` interface provide the following methods:

* `__toString` returns the full string representation of the URL;
* `getRelativeUrl` returns the string representation of the URL without the "domain" parts (ie: `scheme`, `user`, `path`, `host`, `port`);
* `getBaseUrl` returns the string representation of the URL without the "request uri" part (ie: `path`, `query`, `fragment`);
* `sameValueAs` returns `true` if two `League\Url\UrlInterface` object represents the same URL. The comparison is encoding independent.

<p class="message-info">The query string from the URL is automatically encoded following the <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a></p>

~~~.language-php
use League\Url\Url;
use League\Url\UrlImmutable;

$url = Url::createFromUrl('http://www.example.com/path/index.php?query=toto+le+heros');
echo $url->getRelativeUrl(); // /path/index.php?query=toto+le+heros
echo $url->getBaseUrl(); // http://www.example.com
echo $url; // 'http://www.example.com/path/index.php?query=toto+le+heros'

$original_url = Url::createFromUrl('example.com');
$new_url = UrlImmutable::createFromUrl("//example.com");
$alternate_url = Url::createFromUrl('//example.com?foo=toto+le+heros');

$original_url->sameValueAs($new_url); //will return true
$alternate_url->sameValueAs($new_url); //will return false
~~~

## Manipulating Urls

An Url is composed of up to 8 components. Each URLs components can be access and modify through its own setter and getter method from a Url object.

* Chaining is possible since all the setter methods return a `League\Url\UrlInterface` object;
* Getter methods return a [League\Url\Component\ComponentInterface](/components/basic/) object;

Here's a complete list of setter and getter for both classes:

* `setScheme($data)` set the URL scheme component;
* `getScheme()` returns a [League\Url\Components\Scheme](/components/basic/) object
* `setUser($data)` set the URL user component;
* `getUser()` returns a [League\Url\Components\User](/components/basic/)object
* `setPass($data)` set the URL pass component;
* `getPass()` returns a [League\Url\Components\Pass](/components/basic/)object
* `setHost($data)` set the URL host component;
* `getHost()` returns a [League\Url\Components\Host](/components/complex/) object
* `setPort($data)` set the URL port component;
* `getPort()` returns a [League\Url\Components\Port](/components/basic/)object
* `setPath($data)` set the URL path component;
* `getPath()` returns a [League\Url\Components\Path](/components/complex/) object
* `setQuery($data)` set the URL query component;
* `getQuery()` returns a [League\Url\Components\Query](/components/complex/) object
* `setFragment($data)` set the URL fragment component;
* `getFragment()` returns a [League\Url\Components\Fragment](/components/basic/)`object

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;
* another specific component object;
* for `setHost`, `setPath`, `setQuery`: an `array` or a `Traversable` object;

Let's modify a `League\Url\Url` object 

~~~.language-php
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
<li>never modified itself but return a new object instead. 
<li>returns a new property object instead of its own property object to avoid modification by reference.
</ul>
</div>

The same operation using a `League\Url\UrlImmutable` object

~~~.language-php
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
