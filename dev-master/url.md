---
layout: default
title: URLs as Value Objects
---

# The Url object

The library handles URLs through the use of one class, the `League\Url\Url` which implements two interfaces:

* `Psr\Http\Message\UriInterface`
* `League\Url\Interfaces\Url`

<p class="message-warning">While the library validates the host syntax, it does not validate your host against a valid <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## Instantiation

The easiest way to instantiate an URL object is via its named constructors.

### Url::createFromUrl($url = null);

Using the `createFromUrl` static method you can instantiate a new URL object using a string.

Internally, the string will be parse using PHP's `parse_url` function. So any URL parsed by this function will generate a new `League\Url\Url` object.

### Url::createFromServer(array $server)

Using the `createFromServer` method you can instantiate a new `League\Url\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

### Url::createFromComponents(array $components)

Using the `createFromComponents` method you can instantiate a new `League\Url\Url` object from the result of PHP's function `parse_url`.

~~~php
use League\Url\Url;

//Method 1 : from a given URLs
$url = Url::createFromUrl('ftp://host.example.com');

//Method 2: from the current PHP page
//don't forget to provide the $_SERVER array
$url = Url::createFromServer($_SERVER);

//Method 3: create from the result of PHP's parse_url function
$components = parse_url('https://foo.example.com');
$url = Url::createFromComponents($components);
~~~

in all cases `$url` is now a `League\Url\Url` object.

## Outputting the Urls

The `Url` interface provide the following methods to interact with the URLs properties.

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

## Accessing the URLs components

A Url can contain up to 8 components, to ease URL manipulation the class comes with an extensive list of method to access each 8 components:

* `getScheme()` returns a [Scheme][basic] object
* `getUser()` returns a [User][basic] object
* `getPass()` returns a [Pass][basic] object
* `getHost()` returns a [Host](/dev-master/host/) object
* `getPort()` returns a [Port][basic] object
* `getPath()` returns a [Path](/dev-master/path/) object
* `getQuery()` returns a [Query](/dev-master/query/) object
* `getFragment()` returns a [Fragment][basic]`object

Each of these object exposes more methods to deal with each component seperately. Since `Url` is a immutable value object. The returns object are clones of the current object property. so any changes apply to these returned copy won't affect your Url object.

~~~php
$url = Url::createFromUrl('http://www.example.com:443');

$new_port = $url->getPort()->withValue(80);
echo $new_port; // output 80;
echo $url->getPort(); //remains 443
~~~

## Manipulating URLs

The `Url` class is a immutable value object. This means that any modification made to one of its property returns a new instance with the modified property leaving the current object unchanged. This means that chaining is possible since all the setter methods return a new `Url` object;

Here's the complete list of all the setters provider by the class:

* `withScheme($scheme)` set the URL scheme component;
* `withUserInfo($user, $pass)` set the URL userinfo components;
* `withHost($host)` set the URL host component;
* `withPort($port)` set the URL port component;
* `withPath($path)` set the URL path component;
* `withQuery($query)` set the URL query component;
* `withFragment($fragment)` set the URL fragment component;

The arguments for all setters can be:

* `null`;
* a string;
* an object implementing one of the `League\Url` component dedicated interface;
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
~~~

[basic]: /dev-master/component/#simple-components

### Url::resolve

This method helps create new URL relative to the current URL using RFC 3986 rules.

~~~php
$url = Url::createFromUrl('http://www.example.com/path/here/now');
$new_url = $url->resolve('../../../toto');
echo $url; //remains http://www.example.com/path/here/now
echo $new_url; //output http://www.example.com/toto
~~~