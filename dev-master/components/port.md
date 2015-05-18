---
layout: default
title: The Port component
---

# The Port component

The library proves a `League\Url\Port` class to ease port manipulation.

## Port creation

Just like any other component, a new `League\Url\Port` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

~~~php
use League\Url\Port;

$port = new Port(443);
echo $port; //display '443'

$string_port = new Port('443');
echo $string_port; //display '443'

$empty_port = new Port();
echo $empty_port; //display ''
~~~

<p class="message-warning">If the submitted value is not a valid port number an <code>InvalidArgumentException</code> will be thrown.</p>

## Port properties

### Standard ports

When one or more ports are in association with a specific scheme it is called a standard port. To get a list of schemes for which the given Port object is standard you can call the `Port::getStandardSchemes` method.

This method which take no argument returns an array containing a list of `League\Url\Scheme` objects. The array is empty if no scheme is found or if the information is unknown to the package.

~~~php
use League\Url\Port;

$port = new Port(80);
$port->getStandardSchemes(); //returns the equivalent of [new Scheme('http'), new Scheme('ws')];

$port = new Port(22);
$port->getStandardSchemes(); //returns [new Scheme('ssh')];

$port = new Port(324);
$port->getStandardSchemes(); //returns [];
~~~

If you only interested in knowing if the current port is standard to a given scheme you can simply call the `Port::hasStandardScheme` method which takes a scheme or a `League\Url\Scheme` object as its unique argument. The method returns a boolean.

~~~php
use League\Url\Port;

$port = new Port(80);
$port->hasStandardScheme('http'); //returns true

$port = new Port(52);
$port->hasStandardScheme(new Schme('svn+ssh')); //returns false
~~~

## Port representations

### String representation

Basic port representations is done using the following methods:

~~~php
use League\Url\Port;

$port = new Port(21);
$port->__toString();      //return '21'
$port->getUriComponent(); //return ':21'
~~~

### Integer representation

A port can be represented as an integer through the use of the `Port::toInt` method the class.

<p class="message-info">If a Port is not defined, the <code>toInt</code> method returns <code>null</code>.</p>

~~~php
use League\Url\Port;

$port = new Port(81);
$port->toInt(); //returns 81;

$empty_port = new Port();
$empty_port->toInt(); // returns null
~~~

To [compare](/dev-master/components/overview/#components-comparison) or [manipulate](/dev-master/components/overview/#components-modification) the port object you should refer to the component overview section.