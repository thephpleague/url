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

<p class="message-notice">If you try to resolve two Uri objects which do not share the same class. No normalization will occur and the submitted URI object will be return unchanged.</p>

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;

$baseUri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$relativeUri = WsUri::createFromString("./p#~toto");
$modifier    = new Resolve($baseUri);
$newUri      = $modifier->__invoke($relativeUri);
echo $newUri; //displays "./p#~toto"
~~~

## Generating a relative URI

The `Relativize` URI Modifier provides the mean for relativizing an URI according to a referenced base URI.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$baseUri = HttpUri::createFromString("http://www.example.com/this/is/a/long/uri/");
$relativeUri = HttpUri::createFromString("http://www.example.com/short#~toto");
$modifier = new Relativize($baseUri);
$newUri      = $modifier->__invoke($relativeUri);
echo $newUri; //displays "../short#~toto"
~~~

<p class="message-notice">If you try to relativize two Uri object which do not share the same scheme. No normalization will occur and the submitted URI object will be return unchanged.</p>

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;

$baseUri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$relativeUri = WsUri::createFromString("./p#~toto");
$modifier = new Relativize($baseUri);
$newUri      = $modifier->__invoke($relativeUri);
echo $newUri; //displays "./p#~toto"
~~~

<p class="message-notice">At any given time you can create a new modifier with a new base URI using the `withUri` method which expected an URI object or a PSR-7 UriInterface implemented object.</p>


~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;

$baseUri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$relativeUri = WsUri::createFromString("./p#~toto");
$modifier = new Relativize($baseUri);
$altModifier = $modifier->withUri($relativeUri);

// $altModifier is different from $modifier 
~~~