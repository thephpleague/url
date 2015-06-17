---
layout: default
title: The Scheme Registration System
---

# Scheme registration system

Ouf of the box the library supports the following schemes:

- ftp,
- gopher,
- http, https
- ldap, ldaps
- nntp, snews
- ssh,
- ws, wss
- telnet, wais (websockets)
- the empty scheme (which is a pseudo scheme)

But sometimes you may want to work with other schemes. The scheme registration system allow you to extends the `League\Url` functionnalities to other schemes.

### Registering new schemes

To extend the number of supported schemes use a modified `League\Url\Services\SchemeRegistry` object as the second argument of the scheme contructor method like shown below:

~~~php
use League\Url\Scheme;
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->add('yolo', 8080);
$scheme = new Scheme('yolo', $registry); //will now works
$registry->getStandardPort('yolo'); //return [new Port(8080)]
$url = Url::createFromUrl('yolo:/path/to/heaven', $registry); //will now works
~~~

<p class="message-notice">Once attached to a Scheme object, you can no longer alter the <code>SchemeRegistry</code> object.</p>

The `SchemeRegistry::add` method add a new scheme definition using the following arguments:

- The first required argument must be a valid scheme.
- The second optional argument is the standard port associated to it if it exists. It can be expressed as a `League\Url\Port` object, a valid int or an empty string.

If the scheme or the port are invalid an `InvalidArgumentException` exception will be thrown.

~~~php
League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->add('yólo');     //throw a InvalidArgumentException
$registry->add('yolo', -1); //throw a InvalidArgumentException
~~~

If you try to register an already registered scheme an `InvalidArgumentException` exception will be thrown.

~~~php
use League\Url\Scheme;
use League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->add('yOlo', 8080);
$registry->add('yolo', 8020);
~~~

### Is the scheme registered

To know beforehand if a scheme is registered by the library you can use the `SchemeRegistry::has` method like shown below:

~~~php
League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->has('Http'); //returns true;
$registry->has('yolo'); //returns false;
~~~

For instance, following the above example, trying to create a `League\Url\Scheme` **or** a `League\Url\Url` object using the `yolo` scheme will throw an `InvalidArgumentException` exception.

This will not be the case with the example below:

~~~php
League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->add('yOlo', 8080);
$registry->has('Http'); //returns true;
$registry->has('yolo'); //returns true;
~~~

### Unregistering a scheme

At any given time you can removed an additional scheme using the `SchemeRegistry::remove` method like shown below:

~~~php
use League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->add('yOlo', 8080);
$registry->has('yolo'); //returns true;
$registry->remove('yolo');
$registry->has('yolo'); //returns false;
~~~

<p class="message-warning">The registration system <strong>can not modify</strong> the default schemes. Attempt to modify them will result in an <code>InvalidArgumentException</code> being thrown.</p>

### Accessing the SchemeRegistry Object

Since the registry is attached to the `Scheme` object at any given time you can access it using `Scheme:getSchemeRegistry` method.

~~~php
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->add('yolo', 8080);
$url = Url::createFromUrl('yolo:/path/to/heaven', $registry); //will now works
$altRegistry = $url->scheme->getSchemeRegistry();
// $altRegistry is equals but is not the same as $registry
~~~
<p class="message-notice">To preserve the <code>Scheme</code> immutability the method returns a copy of the internal <code>SchemeRegistry</code> object.</p>

### Scheme Standard Ports

When one or several ports are associated to a specific scheme (through their RFCs) the port is called standard. To get a list of those standard ports, you can call the `SchemeRegistry::getStandardPort` method. If the default ports are unknown an empty array will be returned. Otherwise a list of found Port will be return as an array of [League\Url\Port](/4.0/components/port/) objects.

~~~php
use League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->getStandardPort('http'); //returns [new Port(80)]
$registry->getStandardPort('yolo'); //will throw an InvalidArgumentException
                                    //because the scheme 'yolo' is not registered yet
~~~

If you only interested in knowing if a given port is standard you can simply call the `SchemeRegistry::isStandardPort` method which requires the following arguments:

- a scheme string
- a Port number or a League\Url\Port object.

The method returns a boolean.

~~~php
use League\Url\Services\SchemeRegistry

$registry = new SchemeRegistry();
$registry->isStandardPort('http', 80); //returns true
$registry->isStandardPort('http', 81); //returns false
$registry->isStandardPort('yolo', 42); //will throw an InvalidArgumentException
                                       //because the scheme yolo is not registered yet
~~~
