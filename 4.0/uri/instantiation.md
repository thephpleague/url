---
layout: default
title: URIs instantiation
---

# URI Instantiation

## From parse_url results

The easiest way to instantiare a URI object is to use its named constructors `createFromComponents` and give it the result of PHP's function `parse_url`.

~~~php
use League\Uri\Uri;
use League\Uri\Schemes\Registry;

$components = parse_url('https://foo.example.com');
$url = Uri::createFromComponents(new Registry(), $components);
~~~

## From its default constructor

Of course if you already have all the required objects that implements the package interfaces, you can directly instantiate a new `League\Uri\Uri` object from them as shown below:

~~~php
use League\Uri\Uri;

$url = new Uri(
    $schemeRegistry,
	$scheme,
	$userinfo,
	$host,
	$port,
	$path,
	$query,
	$fragment
);

//where $schemeRegistry is a League\Uri\Interfaces\SchemeRegistry implementing object
//where $scheme is a League\Uri\Interfaces\Scheme implementing object
//where $user is a League\Uri\Interfaces\UserInfo implementing object
//where $host is a League\Uri\Interfaces\Host implementing interface
//where $port is a League\Uri\Interfaces\Port implementing object
//where $path is a League\Uri\Interfaces\Path implementing interface
//where $query is a League\Uri\Interfaces\Query implementing interface
//where $fragment is a League\Uri\Interfaces\Fragment implementing object
~~~

<p class="message-warning">If a new instance can not be created a <code>InvalidArgumentException</code> exception is thrown.</p>

## Scheme Registry

The [Scheme registry object](/4.0/uri/scheme-registration/) object is a required object that enables the URI scheme validation against a set of known schemes. If the submitted scheme provided an invalid scheme or an unsupported one an `InvalidArgumentException` exception is thrown.

~~~php
use League\Uri\Uri;
use League\Uri\Scheme\Registry;

$registry = new Registry(['telnet' => 23]);
$telnet   = parse_url('telnet://foo.example.com');
$url = Uri::createFromComponents($telnet, $registry);

$http = parse_url('http://www.example.com');
Uri::createFromComponents($http, $registry);
//will throw an InvalidArgumentException
~~~

In the example above, a new scheme registry is created which only supports the `telnet` scheme. Thus the `League\Uri\Uri::createFromComponents` will:

- correctly instantiated a `telnet` schemed URI;
- throw an exception with an URI using the `http` scheme;

## Web URI

Usually you want to work with one of the following schemes: `http`, `https`, `ftp`, `ws`, `wss`. To ease working with these scheme the library introduces the `Http` class. And because URIs come in different forms we used named constructors to offer several ways to instantiate the object.

### Instantiation

#### From a string

Using the `createFromString` static method you can instantiate a new URI object from a string or from any object that implements the `__toString` method. Internally, the string will be parse using PHP's `parse_url` function.

~~~php
use League\Uri\Schemes\Http;

$url = Http::createFromString('ftp://host.example.com');
~~~

#### From the server variables

Using the `createFromServer` method you can instantiate a new `League\Uri\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

~~~php
use League\Uri\Schemes\Http;

//don't forget to provide the $_SERVER array
$url = Http::createFromServer($_SERVER);
~~~

Because the `Http` class extends the `Uri` class you can use the the `createFromComponents` method or the default constructor to instantiate the object.

<p class="message-warning">If a new instance can not be created a <code>InvalidArgumentException</code> exception is thrown.</p>

### URI Validation

Apart from these named methods, the `Http` class behave exactly like its parent. The only difference is that it disallow URI string and properties changes that might result in invalid URI.

~~~php
use League\Uri\Schemes\Http;
use League\Uri\Schemes\Registry;
use League\Uri\Uri;

$source = 'http:/example.com'; //this is a invalid http URI

$url = Http::createFromString($source);
//will produce a InvalidArgumentException

$components = parse_url($source);
$uri = Uri::createFromComponents(new Registry(), $components);
//No exception is thrown
echo $uri->__toString(); //returns http:/example.com
~~~