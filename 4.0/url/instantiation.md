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

//where $scheme is a League\Interfaces\Scheme implementing object
//where $user is a League\Interfaces\UserInfo implementing object
//where $host is a League\Interfaces\Host implementing interface
//where $port is a League\Interfaces\Port implementing object
//where $path is a League\Interfaces\Path implementing interface
//where $query is a League\Interfaces\Query implementing interface
//where $fragment is a League\Interfaces\Fragment implementing object
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

Instantiating a `League\Url\Url` object with an unknown scheme throws an `InvalidArgumentException` exception. To overcome this limitation, the package provides a [Scheme registry object](/4.0/services/scheme-registration/). With it, you can register your own list of supported schemes and enable the package to work with other schemes. The scheme registry object must be provided as the second parameter of any `League\Url` named constructors.

~~~php
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry(['yolo' => 8020]);
$components = parse_url('yolo://foo.example.com');
$url = Url::createFromComponents($components, $registry);
~~~

In the example above, a new scheme registry is created which only support the `yolo` scheme. Thus the `League\Url\Url` object can be correctly instantiated with a `yolo` schemed URL.

For the default constructor, you don't need to specify any optional argument as the `SchemeRegistry` object is attached to the [Scheme component](/4.0/components/scheme/).