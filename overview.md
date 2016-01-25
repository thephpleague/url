---
layout: default
title: URLs as Value Objects
---

# Overview

The library handles FTP, HTTP and Websocket protocol URLs through the use of two main classes:

* `League\Url\Url` a value object that represents a URL
* `League\Url\UrlImmutable` a immutable value object that represents a URL

Both classes implement the `League\Url\UrlInterface` interface.

Think of PHP `DateTime` and `DateTimeImmutable` classes which implement the `DateTimeInterface` interface.

<p class="message-warning">While the library validates the host syntax, it does not validate your host against a valid <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## Instantiation

Both classes share the same named constructors to ease object instantiation. In the example below I'll use the `League\Url\Url` object as an example, which is also applicable for `League\Url\UrlImmutable`.

~~~php
<?php

use League\Url\Url;

//Method 1 : from a given URLs
$url = Url::createFromUrl('ftp://host.example.com');

//Method 2: from the current PHP page
//don't forget to provide the $_SERVER array
$url = Url::createFromServer($_SERVER);
~~~

`$url` is a `League\Url\Url` object.

## Accessing URL properties

Once you have instantiated a `Url` or a `UrlImmutable` object you can access its properties using the following getter methods:

<p class="message-info">On URL output, the query string is automatically encoded following <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a>.</p>

### UrlInterface::__toString()

Returns the full string representation of the URL;

### UrlInterface::getUserInfo()

Returns the string representation of the URL user info;

### UrlInterface::getAuthority()

Returns the string representation of the URL authority part (ie: `user`, `pass`, `host`, `port`);

### UrlInterface::getBaseUrl()

Returns the string representation of the URL `scheme` component and authority part;

### UrlInterface::getRelativeUrl(UrlInterface $ref_url = null)

<p class="message-notice">the <code>$ref_url</code> argument was added in version <code>3.2</code></p>

Returns the string representation of the URL relative to another `League\Url\UrlInterface` object;


### UrlInterface::sameValueAs(UrlInterface $ref_url)

Returns `true` if two `League\Url\UrlInterface` object represents the same URL.

### UrlInterface::toArray()

<p class="message-notice">added in version <code>3.3</code></p>

Returns the URL component as an array like PHP native `parse_url` but all components are always returned even when missing from the full URL.

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

$url->toArray();
//returns a array with all the component
// array(
//     'scheme' => 'http',
//     'user' => null,
//     'pass' => null,
//     'host' => 'www.example.com',
//     'port' => null,
//     'path' => 'path/index.php',
//     'query' => 'query=toto+le+heros',
//     'fragment' => null,
// );
~~~

## Manipulating URLs

A URL string is composed of 8 components. In `League\Url` each component is represented by a specific object you can accessed on `League\Url\UrlInterface` through their respective setter and getter methods.

* Chaining is possible since all the setter methods return a `League\Url\UrlInterface` object;
* Getter methods return a specific component object;

### Scheme getter and setter

* `UrlInterface::setScheme($data)` set the scheme component;
* `UrlInterface::getScheme()` returns a [League\Url\Components\Scheme][basic] object

### User getter and setter

* `UrlInterface::setUser($data)` set the user component;
* `UrlInterface::getUser()` returns a [League\Url\Components\User][basic] object

### Pass getter and setter

* `UrlInterface::setPass($data)` set the pass component;
* `UrlInterface::getPass()` returns a [League\Url\Components\Pass][basic] object

### Host getter and setter

* `UrlInterface::setHost($data)` set the host component;
* `UrlInterface::getHost()` returns a [League\Url\Components\Host](/components/host/) object

### Port getter and setter

* `UrlInterface::setPort($data)` set the port component;
* `UrlInterface::getPort()` returns a [League\Url\Components\Port][basic] object

### Path getter and setter

* `UrlInterface::setPath($data)` set the path component;
* `UrlInterface::getPath()` returns a [League\Url\Components\Path](/components/path/) object

### Query getter and setter

* `UrlInterface::setQuery($data)` set the query component;
* `UrlInterface::getQuery()` returns a [League\Url\Components\Query](/components/query/) object

### Fragment getter and setter

* `UrlInterface::setFragment($data)` set the fragment component;
* `UrlInterface::getFragment()` returns a [League\Url\Components\Fragment][basic]`object


For all setter methods `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;
* another specific component object;

For the host, path and query components, `$data` can also be an `array` or a `Traversable` object;

## Manipulation examples

Let's modify a `League\Url\Url` object:

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
<li>never modified itself but returns a new object instead.</li>
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