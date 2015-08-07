---
layout: default
title: Http URIs
---

# Http, Https URI

## Instantiation

Usually you want to work with one of the following schemes: `http`, `https`. To ease working with these schemes the library introduces the `Http` class. In addition to the previois named constructor, the `Http` class can be instantiated using the server variables

~~~php
use League\Uri\Schemes\Http as HttpUri;

//don't forget to provide the $_SERVER array
$uri = HttpUri::createFromServer($_SERVER);
~~~

<p class="message-warning">The method only rely on servers safe parameters to determine the current URI. If you are using the library behind a proxy the result may differ from your expectation as no <code>$_SERVER['HTTP_X_*']</code> header is taken into account for security reasons.</p>

## Validation

If a scheme is present a Http URI can not contains an empty authority if its scheme specific part is not empty. Thus, some Http URI modifications must be applied in specific order to preserve the URI validation.

~~~php
use League\Uri\Schemes\Http as HttpUri;

//don't forget to provide the $_SERVER array
$uri = HttpUri::createFromString('http://thephpleague.com/uri/');
echo $uri->withHost('')->withScheme('')->__toString();
// will throw an InvalidArgumentException
// you can not remove the Host if a scheme is present
~~~

Instead you are require to proceed as below

~~~php
use League\Uri\Schemes\Http as HttpUri;

//don't forget to provide the $_SERVER array
$uri = HttpUri::createFromString('http://thephpleague.com/uri/');
echo $uri->withScheme('')->withHost('')->__toString(); //displays "/uri/"
~~~

<p class="message-notice">When an invalid URI object is created a <code>RuntimeException</code> exception is thrown</p>

## Relation with PSR-7

The `Http` class is compliant with the PSR-7 `UriInterface` interface. This means that you can use this class anytime you need a PSR-7 compliant URI object.

## Resolving a relative URI

The Http class provides the mean for resolving an URI as a browser would for an anchor tag. When performing URI resolution the returned URI is normalized according to RFC3986 rules. The uri to resolved must be another `Uri` object.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$newUri = $uri->resolve(HttpUri::createFromString("./p#~toto"));
echo $newUri; //displays "http://www.example.com/hello/p#~toto"
~~~

<p class="message-notice">If you try to resolve two Uri object which do not share the same scheme. No normalization will occur and the submitted URI object will be return unchanged.</p>

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;

$uri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$newUri = $uri->resolve(WsUri::createFromString("./p#~toto"));
echo $newUri; //displays "./p#~toto"
~~~

