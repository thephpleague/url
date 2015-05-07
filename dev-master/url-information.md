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

## Retrieving URL information

### Is the URL absolute ?

A URL is considered absolute if it has a non empty scheme component.

~~~php
$url = Url:createFromUrl('//example.com/foo');
$url->isAbsolute(); //returns false

$url = Url:createFromUrl('ftp:://example.com/foo');
$url->isAbsolute(); //returns true
~~~

### The URL Authority

Sometimes you may want to determine the naming authority that govern the URL name space

