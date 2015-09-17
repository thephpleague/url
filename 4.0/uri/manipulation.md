---
layout: default
title: Manipulating URI
---

# Modifying URIs

<p class="message-notice">If the modifications do not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">The method may throw a <code>RuntimeException</code> exception if the resulting URI is not valid for a scheme specific URI.</p>

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
use League\Uri\Components\Query;
use League\Uri\Schemes\Http as HttpUri;

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
- The callable expects its single argument to be an League URI object or a PSR-7 `UriInterface` object and **must return a instance of the submitted object**.
- If the URI modifier is an object it must be immutable. Updating its parameters must return a new instance with the modified parameters.
- Apart from validating it's own parameters, URI modifiers are transparent when dealing with error and exceptions. They must not alter of silence them.

Let's recreate the above example using a URI modifier.

~~~php
use League\Uri\Components\Query;
use League\Uri\Schemes\Http as HttpUri;

$query = 'foo=bar&taz';
$mergeQuery = function ($uri) use ($query) {
    if (!$uri instanceof League\Uri\Interfaces\Uri 
        && !$uri instanceof Psr\Http\Message\UriInterface) 
    {
        throw new InvalidArgumentException(sprintf(
            'Expected data to be a valid URI object; received "%s"',
            (is_object($uri) ? get_class($uri) : gettype($uri))
        ));
    }
    $currentQuery = new Query($uri->getQuery());
    $updatedQuery = $currentQuery->merge($query)->__toString();

    return $uri->withQuery($updatedQuery);
};

$uri = HttpUri::createFromString("http://www.example.com/the/sky.php?foo=toto#~typo");
$newUri = $mergeQuery($uri);
echo $newUri; // display http://www.example.com/the/sky.php?foo=bar&taz#~typo
~~~

The anonymous function `$mergeQuery` is an rough example of a URI modifier. The library `League\Uri\Modifiers\MergeQuery` [provides a better and more suitable implementation](/4.0/uri/manipulation/query/#merging-query-string).

URI Modifiers can be grouped for simplicity in different categories that deals with

- [manipulating the URI host](/4.0/uri/manipulation/host/);
- [manipulating the URI query](/4.0/uri/manipulation/query/);
- [manipulating the URI path](/4.0/uri/manipulation/path/);
- [manipulating multiple URI components](/4.0/uri/manipulation/generic/);;
