---
layout: default
title: The Scheme component
---

# The Scheme component

The library provides a `League\Url\Scheme` class to ease scheme manipulation.

## Scheme creation

Just like any other component, a new `League\Url\Scheme` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
echo $scheme; //display 'http'


$empty_Scheme = new Scheme();
echo $empty_Scheme; //display ''
~~~

<p class="message-warning">If the submitted value is not a valid scheme or is an unsupported scheme an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a League\Url\Url object

~~~php
use League\Url\Url;

$url  = Url::createFromUrl('http://url.thephpleague.com/');
$scheme = $url->scheme; // $scheme is a League\Url\Scheme object;
~~~

## Scheme Properties

### Scheme Standard Ports

When one or more ports are usually used in association with a specific scheme it is called standard. To get a list of those standard ports, you can call the `Scheme::getStandardPorts` method. If the default ports are unknown an empty array will be returned. Otherwise a list of found Port will be return as an array of [League\Url\Port](/dev-master/components/port/) objects.

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
$scheme->getStandardPorts(); //returns the equivalent of [new Port(80)];

$scheme = new Scheme('svn+ssh');
$scheme->getStandardPorts(); //returns the equivalent of [new Port(22)];
~~~

If you only interested in knowing if a given port is standard you can simply call the `Schme::hasStandardPort` method which takes a Port number or a League\Url\Port object as its unique argument. The method returns a boolean.

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
$scheme->hasStandardPort(80); //returns true

$scheme = new Scheme('svn+ssh');
$scheme->hasStandardPort(80); //returns false
~~~

To [output](/dev-master/components/overview/#components-string-representations), [compare](/dev-master/components/overview/#components-comparison) or [manipulate](/dev-master/components/overview/#components-modification) the Scheme object you should refer to the component overview section.