---
layout: default
title: HTTP URIs instantiation
---

# HTTP URI Instantiation

## Http, Https URI

Usually you want to work with one of the following schemes: `http`, `https`. To ease working with these schemes the library introduces the `Http` class. And because URIs come in different forms we used named constructors to offer several ways to instantiate the object.

## Instantiation

### From a string

Using the `createFromString` static method you can instantiate a new Http URI object from a string or from any object that implements the `__toString` method. Internally, the string will be parse using PHP's `parse_url` function.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$url = HttpUri::createFromString('ftp://host.example.com');
~~~

### From the server variables

Using the `createFromServer` method you can instantiate a new `League\Uri\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

~~~php
use League\Uri\Schemes\Http;

//don't forget to provide the $_SERVER array
$url = Http::createFromServer($_SERVER);
~~~
