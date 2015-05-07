---
layout: default
title: URLs as Value Objects
---

# The Url object

The library handles URLs through the use of one class, the `League\Url\Url` which implements two interfaces:

* `Psr\Http\Message\UriInterface`
* `League\Url\Interfaces\Url`

<p class="message-warning">While the library validates the host syntax, it does not validate your host against a valid <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## Getting URL information

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

### Url::isAbsolute()

Returns whether the current URL is absolute or relative. An URL is considered absolute if it has an non-empty scheme component.

~~~php
use League\Url\Url;

Url::createFromUrl('')->isAbsolute(); // returns false
Url::createFromServer($_SERVER)->isAbsolute(); // returns true
~~~

### Url::hasStandardPort()

Returns whether the current URL uses the default port according the its scheme information.

- If the scheme is not known, the method returns `false`.
- If no port is set, the method will return `true`.

~~~php
use League\Url\Url;

Url::createFromUrl('http://example.com:8042/over/there'')->hasStandardPort(); // returns false
Url::createFromUrl('wss://example.com:443/over/there'')->hasStandardPort(); // returns true
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

### Url::resolve(UriInterface $url)

This method helps create new URL relative to the current URL using RFC 3986 rules.

~~~php
$url = Url::createFromUrl('http://www.example.com/path/here/now');
$new_url = $url->resolve(Url::createFromUrl('../../../toto'));
echo $url; //remains http://www.example.com/path/here/now
echo $new_url; //output http://www.example.com/toto
~~~