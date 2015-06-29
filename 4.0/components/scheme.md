---
layout: default
title: The Scheme component
---

# The Scheme component

The library provides a `League\Uri\Scheme` class to ease scheme manipulation.

## Scheme creation

### Using the default constructor

Just like any other component, a new `League\Uri\Scheme` object can be instantiated using its default constructor.

~~~php
use League\Uri\Scheme;

$scheme = new Scheme('http');
echo $scheme; //display 'http'


$empty_scheme = new Scheme();
echo $empty_scheme; //display ''
~~~

Ouf of the box the library supports the following schemes:

- ftp,
- file,
- http, https
- ssh,
- ws, wss

If you try to instantiate a scheme object with a different scheme and `InvalidArgumentException` exception will be thrown. To overcome this limitation, the scheme constructor can take an optional second argument which is a `SchemeRegistry` class. Depending the registry values you will be able to instantiate other schemes like shown below:

~~~php
use League\Uri\Scheme;
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->merge(['telnet' => 23]);
$scheme = new Scheme('telnet', $newRegistry); //will now works
~~~

At any given time you can get access to the `SchemeRegistry` object using the `getSchemeRegistry` method.

~~~php
use League\Uri\Scheme;

$scheme = new Scheme('file');
$registry = $scheme->getSchemeRegistry();
$registry->hasOffset('file'); //return true
~~~

Get more informations about the [SchemeRegistry class](/4.0/services/scheme-registration/)

### Using a League\Uri\Url object

Another way to acces to a `League\Uri\Scheme` is to use an already instantiated `League\Uri\Url` object.

~~~php
use League\Uri\Url;

$url  = Url::createFromString('http://url.thephpleague.com/');
$scheme = $url->scheme; // $scheme is a League\Uri\Scheme object;
~~~

<p class="message-warning">If the submitted value is not a valid scheme an <code>InvalidArgumentException</code> will be thrown.</p>

To [output](/4.0/components/overview/#components-string-representations), [compare](/4.0/components/overview/#components-comparison) or [manipulate](/4.0/components/overview/#components-modification) the `Scheme` object you should refer to the component overview section.