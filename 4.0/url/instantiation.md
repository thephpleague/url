---
layout: default
title: URLs instantiation
---

# URL Instantiation

Because URLs come in different forms we used named constructors to offer several ways to instantiate the library objects.

## From an URL string

Using the `createFromUrl` static method you can instantiate a new URL object from a string.

Internally, the string will be parse using PHP's `parse_url` function. So any URL parsed by this function will generate a new `League\Url\Url` object.

~~~php
use League\Url\Url;

$url = Url::createFromUrl('ftp://host.example.com');
~~~

## From the environment

Using the `createFromServer` method you can instantiate a new `League\Url\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

~~~php
use League\Url\Url;

//don't forget to provide the $_SERVER array
$url = Url::createFromServer($_SERVER);
~~~

## From parse_url results

Using the `createFromComponents` method you can instantiate a new `League\Url\Url` object from the result of PHP's function `parse_url`.

~~~php
use League\Url\Url;

$components = parse_url('https://foo.example.com');
$url = Url::createFromComponents($components);
~~~

## From its default constructor

If you already have all the URLs components as object that implements the package interfaces, you can directly instantiate a new `League\Url\Url` object from them.

~~~php
use League\Url\Url;

$url = new Url(
	$scheme,
	$userinfo,
	$host,
	$port,
	$path,
	$query,
	$fragment
);

//where $scheme is a League\Url\Scheme object
//where $user is a League\Url\UserInfo object
//where $host is a League\Interfaces\Host interface
//where $port is a League\Url\Port object
//where $path is a League\Interfaces\Path interface
//where $query is a League\Interfaces\Query interface
//where $fragment is a League\Url\Fragment object
~~~

<p class="message-warning">If the submitted value can not return a new instance a <code>InvalidArgumentException</code> exception will be thrown.</p>

## Scheme Limitations

Ouf of the box the library supports the following schemes:

- ftp, ftps
- file,
- gopher,
- http, https
- ldap, ldaps
- nntp, snews
- ssh,
- ws, wss
- telnet, wais

Instantiating a `League\Url\Url` object with an unknown scheme throws an `InvalidArgumentException` exception. To overcome this limitation, the package provide a [Scheme registration system](/4.0/services/scheme-registration/). You can register a new scheme into the registry object and provide this object as the second parameter of any `League\Url` named constructors.

~~~php
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = (new SchemeRegistry())->merge(['yolo' => 8020]);
$components = parse_url('yolo://foo.example.com');
$url = Url::createFromComponents($components, $registry);
~~~

In the example above, the `yolo` scheme is added to the scheme registry and the `League\Url\Url` object can be correctly instantiated.

For the default constructor, the `SchemeRegistry` object is loaded using the [Scheme constructor](/4.0/components/scheme/).