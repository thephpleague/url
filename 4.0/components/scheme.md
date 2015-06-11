---
layout: default
title: The Scheme component
---

# The Scheme component

The library provides a `League\Url\Scheme` class to ease scheme manipulation.

## Scheme creation

### Using the default constructor

Just like any other component, a new `League\Url\Scheme` object can be instantiated using its default constructor.

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
echo $scheme; //display 'http'


$empty_Scheme = new Scheme();
echo $empty_Scheme; //display ''
~~~

### Using a League\Url\Url object

Another way to acces to `League\Url\Scheme` is to use a already instantiated `League\Url\Url` object.

~~~php
use League\Url\Url;

$url  = Url::createFromUrl('http://url.thephpleague.com/');
$scheme = $url->scheme; // $scheme is a League\Url\Scheme object;
~~~

<p class="message-warning">If the submitted value is not a valid scheme or is an unregisterd scheme an <code>InvalidArgumentException</code> will be thrown.</p>

## Scheme Properties

### Scheme Standard Ports

When one or several ports are associated to a specific scheme (through their RFCs) the port is called standard. To get a list of those standard ports, you can call the `Scheme::getStandardPorts` method. If the default ports are unknown an empty array will be returned. Otherwise a list of found Port will be return as an array of [League\Url\Port](/4.0/components/port/) objects.

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
$scheme->getStandardPorts(); //returns the equivalent of [new Port(80)];
~~~

If you only interested in knowing if a given port is standard you can simply call the `Schme::hasStandardPort` method which takes a Port number or a League\Url\Port object as its unique argument. The method returns a boolean.

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
$scheme->hasStandardPort(80); //returns true
$scheme->hasStandardPort(81); //returns false
~~~

To [output](/4.0/components/overview/#components-string-representations), [compare](/4.0/components/overview/#components-comparison) or [manipulate](/4.0/components/overview/#components-modification) the `Scheme` object you should refer to the component overview section.

## Scheme registration system

Ouf of the box the library supports the following standard schemes:

- http, https (HTTP protocols)
- ftp, ftps, (FTP protocols)
- ws, wss (websockets)
- file (legacy)

### Registering additional schemes

If you still want to use the library with other schemes, you can easily extends the number of supported protocols by calling the static method `Scheme::register` as shown below:

~~~php
use League\Url\Scheme;
use League\Url\Url;

Scheme::register('yOlo', 8080);
$scheme = Scheme('yolo'); //will now works
$scheme->getStandardPorts(); //return [8080]
$url = Url::createFromUrl('yolo:/path/to/heaven'); //will now works
~~~

- The first required argument must be a valid scheme.
- The second optional argument is the standard port associated to it if it exists.

If the scheme or the port are invalid a `InvalidArgumentException` exception will be thrown.

~~~php
use League\Url\Scheme;

Scheme::register('yólo');     //throw a InvalidArgumentException
Scheme::register('yolo', -1); //throw a InvalidArgumentException
~~~

You can registered multiple standard ports for a given scheme.

~~~php
use League\Url\Scheme;

Scheme::register('yOlo', 8080);
Scheme::register('yolo', 8020);
$scheme = Scheme('yolo');
$scheme->getStandardPorts(); //return [8020, 8080]
~~~

### Is the scheme registered

To know beforehand if a scheme is registered by the library you can use the `Scheme::isRegistered` static method like shown below:

~~~php
use League\Url\Scheme;

Scheme::isRegistered('Http'); //returns true;
Scheme::isRegistered('yolo'); //returns false;
~~~

For instance, following the above example, trying to create a `League\Url\Scheme` **or** a `League\Url\Url` object using the `yolo` scheme will throw an `InvalidArgumentException` exception.

~~~php
use League\Url\Scheme;

Scheme::register('yOlo', 8080);
Scheme::isRegistered('Http'); //returns true;
Scheme::isRegistered('yolo'); //returns true;
~~~

Conversely, once registered the `yolo` scheme becomes available for both objects.

### Unregistered a scheme

At any given time you can unregistered an additional scheme using the `Scheme::unRegister` static method like shown below:

~~~php
use League\Url\Scheme;

Scheme::register('yOlo', 8080);
Scheme::isRegistered('yolo'); //returns true;
Scheme::unRegister('yolo');
Scheme::isRegistered('yolo'); //returns false;
~~~

<p class="message-warning">The registration system <strong>can not modify</strong> the default schemes. Attempt to modify them will result in an <code>InvalidArgumentException</code> being thrown.</p>
