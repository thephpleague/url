---
layout: default
title: URIs instantiation
---

# URI Instantiation

Because URIs come in different forms we used named constructors to offer several ways to instantiate the library objects.

## From a string

Using the `createFromString` static method you can instantiate a new URI object from a string or from any object that implements the `__toString` method. Internally, the string will be parse using PHP's `parse_url` function.

~~~php
use League\Uri\Uri;

$url = Uri::createFromString('ftp://host.example.com');
~~~

## From the server variables

Using the `createFromServer` method you can instantiate a new `League\Uri\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

~~~php
use League\Uri\Uri;

//don't forget to provide the $_SERVER array
$url = Uri::createFromServer($_SERVER);
~~~

## From parse_url results

Using the `createFromComponents` method you can instantiate a new `League\Uri\Url` object from the result of PHP's function `parse_url`.

~~~php
use League\Uri\Uri;

$components = parse_url('https://foo.example.com');
$url = Uri::createFromComponents($components);
~~~

## From its default constructor

Of course if you already have all the required objects that implements the package interfaces, you can directly instantiate a new `League\Uri\Url` object from them as shown below:

~~~php
use League\Uri\Uri;

$url = new Url(
	$scheme,
	$userinfo,
	$host,
	$port,
	$path,
	$query,
	$fragment,
    $schemeRegistry
);

//where $scheme is a League\Uri\Interfaces\Scheme implementing object
//where $user is a League\Uri\Interfaces\UserInfo implementing object
//where $host is a League\Uri\Interfaces\Host implementing interface
//where $port is a League\Uri\Interfaces\Port implementing object
//where $path is a League\Uri\Interfaces\Path implementing interface
//where $query is a League\Uri\Interfaces\Query implementing interface
//where $fragment is a League\Uri\Interfaces\Fragment implementing object
//where $schemeRegistry is a League\Uri\Interfaces\SchemeRegistry implementing object
~~~

<p class="message-warning">If a new instance can not be created a <code>InvalidArgumentException</code> exception is thrown.</p>

## Scheme Limitations

Ouf of the box the library supports the following schemes:

- ftp,
- http, https
- ws, wss

Instantiating a `League\Uri\Url` object with an unsupported/unknown scheme throws an `InvalidArgumentException` exception. To overcome this limitation, the package provides a [Scheme registry object](/4.0/services/scheme-registration/) to enable total control over the supported schemes. The scheme registry object must be provided as the second parameter of any `League\Uri\Url` named constructors.

~~~php
use League\Uri\Uri;
use League\Uri\Scheme\Registry;

$registry   = new Registry(['telnet' => 23]);
$components = parse_url('telnet://foo.example.com');
$url = Uri::createFromComponents($components, $registry);
Uri::createFromString('http://www.example.com', $registry);
//will throw an InvalidArgumentException
~~~

In the example above, a new scheme registry is created which only supports the `telnet` scheme. Thus the `League\Uri\Uri::createFromComponents` will:

- correctly instantiated a `telnet` schemed URI;
- throw an exception with an URI using the `http` scheme;