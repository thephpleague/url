---
layout: default
title: Getting URLs information
---

# URL Properties

Once the Url object is instantiated, the object provides you with the a lot of information regarding its properties

## Accessing the URLs components

A Url can contain up to 8 components, to ease URL manipulation the class comes with getter methods to access each of them:

* `getScheme()`
* `getUser()`
* `getPass()`
* `getHost()`
* `getPort()`
* `getPath()`
* `getQuery()`
* `getFragment()`

Of note, each of these methods returns a stringable immutable value object. These objects are clones of the URL component so that any changes apply to these returned copy won't affect your Url object.

~~~php
$url = Url::createFromUrl('http://www.example.com:443');

$new_port = $url->getPort()->withValue(80);
echo $url->getPort(); //remains 443
echo $new_port; // output 80;
~~~


### Is the URL absolute ?

An URL is absolute if it contains an non empty scheme

~~~php
$url = Url:createFromUrl('//example.com/foo');
$url->isAbsolute(); //returns false

$url = Url:createFromUrl('ftp:://example.com/foo');
$url->isAbsolute(); //returns true
~~~

### The URL Authority

Using the `createFromServer` method you can instantiate a new `League\Url\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

~~~php
use League\Url\Url;

//don't forget to provide the $_SERVER array
$url = Url::createFromServer($_SERVER);
~~~

### From parse_url results

Using the `createFromComponents` method you can instantiate a new `League\Url\Url` object from the result of PHP's function `parse_url`.

~~~php
use League\Url\Url;

$components = parse_url('https://foo.example.com');
$url = Url::createFromComponents($components);
~~~

### From its default constructor

If you already have a all components as object that implements the package interfaces, you can directly instantiate a new `League\Url\Url` object from them.

~~~php
use League\Url\Url;

$url = new Url(
	$scheme,
	$user,
	$pass,
	$host,
	$port,
	$path,
	$query,
	$fragment
);

//where $scheme is a League\Url\Scheme object
//where $user is a League\Url\User object
//where $pass is a League\Url\Pass object
//where $host is a League\Interfaces\Host interface
//where $port is a League\Url\Port object
//where $path is a League\Interfaces\Path interface
//where $query is a League\Interfaces\Query interface
//where $fragment is a League\Url\Fragment object
~~~
