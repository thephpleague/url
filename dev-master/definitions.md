---
layout: default
title: URLs as Value Objects
---

# Urls as Value Objects

The League URL package models URLs and URLs components as [immutable](http://en.wikipedia.org/wiki/Immutable_object) [value objects](http://en.wikipedia.org/wiki/Value_object).

> The term "Uniform Resource Locator" (URL) refers to the subset of URIs that, in addition to identifying a resource, provide a means of locating the resource by describing its primary access mechanism. [RFC3986](http://tools.ietf.org/html/rfc3986#section-1.1.3)

This means that a URL is like a street address, if you omit or change even a single character in it, you won't be able to identy, to find what your were looking for. This is exactly the definition of a value object.

~~~php
use League\Url\Url;

$url1 = Url::createFromUrl("http://example.com:81/toto");
$url2 = Url::createFromUrl("http://example.com:82/toto");
//represent 2 different URLs with different port component.
$url1->sameValueAs($url2); //return false;
~~~

To ease and ensure the integrity of the value, when a component is altered instead of modifying its current value, we return a new component with the changed value. This practice is called immutability.

~~~php
$url1 = Url::createFromUrl("http://example.com:81/toto");
$url2 = $url1->withPort(82);
echo $url1; //still displays "http://example.com:81/toto"
echo $url2; //displays "http://example.com:82/toto"
~~~

With both of these concepts, the package enforces stronger and efficient manipulation of URLs and its different components.