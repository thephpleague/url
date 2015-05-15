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

$string_port = new Path('443');
echo $string_port; //display '443'

$empty_port = new Path();
echo $empty_port; //display ''
~~~

<p class="message-warning">If the submitted value is not a valid port number an <code>InvalidArgumentException</code> will be thrown.</p>

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