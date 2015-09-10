---
layout: default
title: Http URIs
---

# Http, Https URI

## Instantiation

Usually you want to work with one of the following schemes: `http`, `https`. To ease working with these schemes the library introduces the `Http` class. In addition to the [defined named constructors](/4.0/uri/instantiation/#uri-instantiation), the `Http` class can be instantiated using the server variables.

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
$uri = HttpUri::createFromString('http://url.thephpleague.com/4.0/');
echo $uri->withHost('')->withScheme('')->__toString();
// will throw an InvalidArgumentException
// you can not remove the Host if a scheme is present
~~~

Instead you are require to proceed as below

~~~php
use League\Uri\Schemes\Http as HttpUri;

//don't forget to provide the $_SERVER array
$uri = HttpUri::createFromString('http://url.thephpleague.com/4.0/');
echo $uri->withScheme('')->withHost('')->__toString(); //displays "/4.0/"
~~~

<p class="message-notice">When an invalid URI object is created a <code>RuntimeException</code> exception is thrown</p>

## Relation with PSR-7

The `Http` class is compliant with the PSR-7 `UriInterface` interface. This means that you can use this class anytime you need a PSR-7 compliant URI object.

## Properties

The Http URI class uses the [HierarchicalPath](/4.0/components/hierarchical-path/) class to represents its path. using PHP's magic `__get` method you can access the object path and get more informations about the underlying path.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString('http://url.thephpleague.com/4.0/uri/schemes/http.md');
echo $uri->path->getBasename(); //returns 'http.md'
echo $uri->path->getDirname(); //returns '/4.0/uri/schemes'
echo $uri->path->toArray(); //returns an array representation of the path segments
$uri->path->isAbsolute(); //returns true
...
~~~