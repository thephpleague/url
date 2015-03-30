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

The easiest way to instantiate an URL object is via its named constructors.

### Url::createFromUrl($url = null);

Using the `createFromUrl` static method you can instantiate a new URL object using a string.

Internally, the string will be parse using PHP's `parse_url` function. So any URL parsed by this function will generate a new `League\Url\Url` object.

### Url::createFromServer(array $server)

Using the `createFromServer` method you can instantiate a new `League\Url\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

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

The `UrlInterface` interface provide the following methods to interact with the URLs properties.

### Url::__toString

Returns the string representation of a `UriInterface` object as a valid URL.

~~~php

use League\Url\Url;

$url = Url::createFromServer($_SERVER);
echo $url; // returns 'http://www.example.com'
~~~

### Url::getUserInfo()

Returns the string representation of the URL user info;

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
echo $url->getUserInfo(); // returns 'user:password';
~~~

### Url::getAuthority();

Returns the string representation of the URL authority part (ie: `user`, `pass`, `host`, `port`);

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
echo $url->getAuthority(); // returns 'user:password@example.com:8042';
~~~

### Url::getBaseUrl();

Returns the string representation of the URL `scheme` component prepending the authority part;

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
echo $url->getBaseUrl(); // returns 'http://user:password@example.com:8042';
~~~

### Url::sameValueAs(UriInterface $url)

Tells whether two `Psr\Http\Message\UriInterface` objects share the same string representation.

~~~php

use League\Url\Url;

$url = Url::createFromUrl('http://user:password@example.com:8042/over/there');
$ref = Url::createFromServer($_SERVER);
$alt = Url::createFromServer($_SERVER);
echo $url->sameValuesAs($ref); // returns true if $ref->__toString() == $url->__toString()
echo $ref->sameValueAs($alt); //will return true
~~~

### Url::toArray()

Returns an array representation of the URL similar php `parse_url` function. The difference being that the `toArray` will return all Urls components even those that are not set.

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

## Manipulating URLs

A URL string is composed of up to 8 components. Each URL component can be accessed and modified through its own setter and getter method.

* Chaining is possible since all the setter methods return a `UrlInterface` object;
* Getter methods return an object which implements at least the [League\Url\Interfaces\Component][basic] interface;

Here's a complete list of all the setter and getter provided by the `UrlInterface` interface:

* `withScheme($scheme)` set the URL scheme component;
* `getScheme()` returns a [Scheme][basic] object
* `withUserInfo($user, $pass)` set the URL userinfo components;
* `getUser()` returns a [User][basic] object
* `getPass()` returns a [Pass][basic] object
* `withHost($host)` set the URL host component;
* `getHost()` returns a [Host](/dev-master/host/) object
* `withPort($port)` set the URL port component;
* `getPort()` returns a [Port][basic] object
* `withPath($path)` set the URL path component;
* `getPath()` returns a [Path](/dev-master/path/) object
* `withQuery($query)` set the URL query component;
* `getQuery()` returns a [Query](/dev-master/query/) object
* `withFragment($fragment)` set the URL fragment component;
* `getFragment()` returns a [Fragment][basic]`object

The arguments can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;

Let's modify a `League\Url\Url` object:


~~~php
$url = Url::createFromUrl('http://www.example.com');
$new_url = $url
	->withUser('john', 'doe')
	->withPort(443)
	->withScheme('https');
echo $url; //remains http://www.example.com/
echo $new_url; //output https://john:doe@www.example.com:443/

$new_port = $new_url->getPort()->withValue(80);
echo $new_port; // output 80;
echo $new_url->getPort(); //remains 443
~~~

[basic]: /dev-master/component/#simple-components