---
layout: default
title: URLs as Value Objects
---

# The Url object

The library handles URLs through the use of one class, the `League\Url\Url` which implements two interfaces:

* `Psr\Http\Message\UriInterface`
* `League\Url\Interfaces`

<p class="message-warning">While the library validates the host syntax, it does not validate your host against a valid <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## Instantiation

### Named constructors

The easiest way to instantiate an URL object is via its named constructors.

### Url::createFromUrl($url = null);

Using the `createFromUrl` you can instantiate a new URL object using a string. Internally, the string will be parse using PHP's `parse_url` function. So any URL parsed by this function will generate a new League\Url\Url object.

### Url::createFromServer(array $server)

Using the `createFromServer` you can instantiate a new URL object from PHP's server variables. Of not you must specify the array containing the variable usually `$_SERVER`.

~~~php
<?php

use League\Url\Url;

//Method 1 : from a given URLs
$url = Url::createFromUrl('ftp://host.example.com');

//Method 2: from the current PHP page
//don't forget to provide the $_SERVER array
$url = Url::createFromServer($_SERVER);
~~~

in both cases `$url` is now a `League\Url\Url` object.

## Outputting the Urls

The `UrlInterface` interface provide the following methods:

### UrlInterface::__toString

Returns the string representation of a `UrlInterface` object as a valid URL.

~~~php

use League\Url\Url;

$url = Url::createFromServer($_SERVER);
echo $url; // returns 'http://www.example.com'
~~~

### UrlInterface::getUserInfo()

Returns the string representation of the URL user info;

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
echo $url->getUserInfo(); // returns 'user:password';
~~~

### UrlInterface::getAuthority();

Returns the string representation of the URL authority part (ie: `user`, `pass`, `host`, `port`);

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
echo $url->getAuthority(); // returns 'user:password@example.com:8042';
~~~

### UrlInterface::getBaseUrl();

Returns the string representation of the URL `scheme` component prepending the authority part;

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
echo $url->getBaseUrl(); // returns 'http://user:password@example.com:8042';
~~~

### UrlInterface::sameValueAs(UriInterface $url)

Tells whether two `Psr\Http\Message\UriInterface` objects share the same string representation. The comparison is done using the final `__toString` representation.

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
$ref = Url::createFromServer($_SERVER);
$alt = Url::createFromServer($_SERVER);
echo $url->sameValuesAs($ref); // returns true if $ref->__toString() == $url->__toString()
echo $ref->sameValueAs($alt); //will return true
~~~

### UrlInterface::toArray()

Returns an array representation of the URL similar php `parse_url` function.

~~~php

use League\Url\UrlImmutable;

$url = UrlImmutable::createFromUrl('http://user:password@example.com:8042/over/there');
$arr = $url->toArray();
//returns the following array
// [
//   'scheme' => 'http',
//   'user' => 'user',
//   'pass' => 'password',
//   'host' => 'example.com',
//   'port' => 8042,
//   'path' => 'over/there',
//   'query' => null,
//   'fragment' => null,
// ]
~~~

<p class="message-info">On URL output, the query string is automatically encoded following <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a>.</p>

## Manipulating URLs

A URL string is composed of up to 8 components. For each object, each URL component can be accessed and modified through its own setter and getter method.

* Chaining is possible since all the setter methods return a `UrlInterface` object;
* Getter methods return an object which implements at least the [League\Url\Interfaces\ComponentInterface][basic] interface;

Here's a complete list of all the setter and getter provided by the `UrlInterface` interface:

* `setScheme($data)` set the URL scheme component;
* `getScheme()` returns a [Scheme][basic] object
* `setUser($data)` set the URL user component;
* `getUser()` returns a [User][basic] object
* `setPass($data)` set the URL pass component;
* `getPass()` returns a [Pass][basic] object
* `setHost($data)` set the URL host component;
* `getHost()` returns a [Host](/dev-master/host/) object
* `setPort($data)` set the URL port component;
* `getPort()` returns a [Port][basic] object
* `setPath($data)` set the URL path component;
* `getPath()` returns a [Path](/dev-master/path/) object
* `setQuery($data)` set the URL query component;
* `getQuery()` returns a [Query](/dev-master/query/) object
* `setFragment($data)` set the URL fragment component;
* `getFragment()` returns a [Fragment][basic]`object

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;
* an object implementing the [ComponentInterface][basic] interface;
* for `setHost`, `setPath`, `setQuery`: an `array` or a `Traversable` object;

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

[basic]: /dev-master/component/#simple-components