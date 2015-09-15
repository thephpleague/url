---
layout: default
title: URI Modifiers which affect multiple URI components
---

# Generic URI Modifiers

## Resolving a relative URI

The `Resolve` URI Modifier provides the mean for resolving an URI as a browser would for an anchor tag. When performing URI resolution the returned URI is normalized according to RFC3986 rules. The uri to resolved must be another Uri object.

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Modifiers\Resolve;

$baseUri     = HttpUri::createFromString("http://www.example.com/path/to/the/sky/");
$relativeUri = HttpUri::createFromString("./p#~toto");
$modifier    = new Resolve($baseUri);
$newUri = $modifier->__invoke($relativeUri);
echo $newUri; //displays "http://www.example.com/hello/p#~toto"
~~~

## Generating a relative URI

The `Relativize` URI Modifier provides the mean for relativizing an URI according to a referenced base URI.

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Modifiers\Relativize;

$baseUri = HttpUri::createFromString("http://www.example.com/this/is/a/long/uri/");
$relativeUri = HttpUri::createFromString("http://www.example.com/short#~toto");
$modifier = new Relativize($baseUri);
$newUri      = $modifier->__invoke($relativeUri);
echo $newUri; //displays "../short#~toto"
~~~

## Modifying the base URI

For both modifiers, you can, at any given time, update the base URI using the <code>withUri</code> method which expected an URI object or a PSR-7 UriInterface implemented object.

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;
use League\Uri\Modifiers\Relativize;

$baseUri = HttpUri::createFromString("http://www.example.com/this/is/a/long/uri/");
$relativeUri = HttpUri::createFromString("http://www.example.com/short#~toto");
$modifier = new Relativize($baseUri);
$newUri      = $modifier->__invoke($relativeUri);
echo $newUri; //displays "../short#~toto"
$altUri = HttpUri::createFromString("http://www.example.com/");
$altModifier = $modifier->newUri($altUri);
$altUri = $altModifier->__invoke($relativeUri);
echo $altUri; //displays 




// $altModifier is different from $modifier 
~~~