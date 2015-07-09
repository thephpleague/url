---
layout: default
title: The Scheme component
---

# The Scheme component

The library provides a `League\Uri\Scheme` class to ease scheme manipulation.

## Scheme creation

### Using the default constructor

A new `League\Uri\Scheme` object can be instantiated using its default constructor.

~~~php
use League\Uri\Scheme;

$scheme = new Scheme('http');
echo $scheme; //display 'http'


$empty_scheme = new Scheme();
echo $empty_scheme; //display ''
~~~

<p class="message-warning">If the submitted value is not a valid scheme an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a League\Uri\Url object

Another way to get acces to a `League\Uri\Scheme` is to use an already instantiated `League\Uri\Url` object.

~~~php
use League\Uri\Uri;

$url  = Uri::createFromString('http://url.thephpleague.com/');
$scheme = $url->scheme; // $scheme is a League\Uri\Scheme object;
~~~

To [output](/4.0/components/overview/#components-string-representations), [compare](/4.0/components/overview/#components-comparison) or [manipulate](/4.0/components/overview/#components-modification) the `Scheme` object you should refer to the component overview section.