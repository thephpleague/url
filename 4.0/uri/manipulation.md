---
layout: default
title: Manipulating URI
---

# Modifying URIs

<p class="message-notice">If the modifications do not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">The method may throw an <code>InvalidArgumentException</code> if the resulting URI is not valid for a scheme specific URI.</p>

## Basic modifications

To completely replace one of the URI part you can use the modifying methods exposed by all URI object

~~~php
use League\Uri\Schemes\Ws as WsUri;

$uri = WsUri::createFromString("ws://thephpleague.com/fr/")
    ->withScheme("wss")
    ->withUserInfo("foo", "bar")
    ->withHost("www.example.com")
    ->withPort(81)
    ->withPath("/how/are/you")
    ->withQuery("foo=baz");

echo $uri; //displays wss://foo:bar@www.example.com:81/how/are/you?foo=baz
~~~

Since All URI object are immutable you can chain each modifying methods to simplify URI creation and/or modification.

## URI modifiers

Often what you really want is to partially update one of the URI component. Using the current public API it is possible but requires several intermediary steps. For instance here's how you would update the query string from a given URI object:

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Components\Query;

$uri         = HttpUri::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$uriQuery    = new Query($uri->getQuery());
$updateQuery = $uriQuery->merge("foo=bar&taz=");
$newUri      = $uri->withQuery($updateQuery->__toString());
echo $newUri; // display http://www.example.com/the/sky.php?foo=bar&taz#~typo
~~~

### URI modifiers principles

To ease these operations the package introduces the concept of URI modifiers

A URI modifier must follow the following rules:

- It must be a callable. If the URI modifier is a class it must implement PHP's `__invoke` method.
- The callable expects its single argument to be an URI object or a PSR-7 UriInterface object and must return a instance of the submitted object.
- If the URI modifier is an obejct it must be a immutable. Updating its parameters must return a new instance with the modified parameters.
- Apart from validating it's own parameters, URI modifiers are transparent when dealing with error and exceptions. They must not alter of silence them.

### League URI pipeline

Since all modifiers returns a URI object instance it is possible to chain them together. To ease this chaining the package comes bundle with the `League\Uri\Pipeline` class. This class uses the pipeline pattern to modify the URI by passing the results from one modifier to the next one. The `League\Uri\Pipeline` can also be used as a URI modifier as well which can lead to advance modification from you URI in a sane an normalized way.

~~~php
use League\Uri\Modifiers\RemoveDotSegments;
use League\Uri\Modifiers\HostToAscii;
use League\Uri\Modifiers\KsortQuery;
use League\Uri\Pipeline;
use League\Uri\Schemes\Http as HttpUri;

$origUri = HttpUri::createFromString("http://스타벅스코리아.com/to/the/sky/");
$origUri2 = HttpUri::createFromString("http://xn--oy2b35ckwhba574atvuzkc.com/path/../to/the/./sky/");

$modifier = (new Pipeline())
	->pipe(new RemoveDotSegment())
	->pipe(new HostToAscii())
	->pipe(new KsortQuery());

$origUri1Alt = $modifier->__invoke($origUri1);
$origUri2Alt = $modifier->__invoke($origUri2);

echo $origUri1Alt; //display http://xn--oy2b35ckwhba574atvuzkc.com/to/the/sky/
echo $origUri2Alt; //display http://xn--oy2b35ckwhba574atvuzkc.com/to/the/sky/
~~~

URI Modifiers can be grouped for simplicity in different categories that deals with

- [the URI host](/4.0/uri/manipulation/host/);
- [the URI query](/4.0/uri/manipulation/query/);
- [the URI path](/4.0/uri/manipulation/path/);
- [multiple URI components](/4.0/uri/manipulation/generic/);;
