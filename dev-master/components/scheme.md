---
layout: default
title: The Scheme component
---

# The Scheme component

The library proves a `League\Url\Scheme` class to ease Scheme manipulation.

## Scheme creation

Just like any other component, a new `League\Url\Scheme` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
echo $scheme; //display 'http'


$empty_Scheme = new Scheme();
echo $empty_Scheme; //display ''
~~~

<p class="message-warning">If the submitted value is not a valid Scheme number an <code>InvalidArgumentException</code> will be thrown.</p>

## Scheme Properties

### Scheme Standard Ports

When one or more ports are usually used in association with a specific scheme it is called standard. To get a list of those standard ports, you can call the `Scheme::getStandardPorts` method. If the default ports are unknown an empty array will be returned. Otherwise a list of found Port number will be return sorted numerically.

~~~php
use League\Url\Scheme;

$scheme = new Scheme('http');
$scheme->getStandardPorts(); //returns [80];

$scheme = new Scheme('svn+ssh');
$scheme->getStandardPorts(); //returns [22];

$scheme = new Scheme('yolo');
$scheme->getStandardPorts(); //returns [];
~~~

To [output](/dev-master/components/overview/#components-string-representations), [compare](/dev-master/components/overview/#components-comparison) or [manipulate](/dev-master/components/overview/#components-modification) the Scheme object you should refer to the component overview section.