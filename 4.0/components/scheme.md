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

Ouf of the box the library supports the following schemes:

- ftp, ftps
- file,
- gopher,
- http, https
- ldap, ldaps
- nntp, snews
- ssh,
- ws, wss
- telnet, wais

If you try to instantiate a scheme object with a different scheme and `InvalidArgumentException` exception will be thrown. To overcome this limitation, the scheme constructor can take an optional second argument which is a `SchemeRegistry` class. Depending on the values the registry object contains you will be able to instantiate other schemes like shown below:


~~~php
use League\Url\Scheme;
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = (new SchemeRegistry())->merge(['yolo' => null]);
$scheme = new Scheme('yolo', $registry); //will now works
$url = Url::createFromUrl('yolo:/path/to/heaven', $registry); //will now works
~~~

Get more informations about the [SchemeRegistry class](/4.0/services/scheme-registration/)

### Using a League\Url\Url object

Another way to acces to `League\Url\Scheme` is to use a already instantiated `League\Url\Url` object.

~~~php
use League\Url\Url;

$url  = Url::createFromUrl('http://url.thephpleague.com/');
$scheme = $url->scheme; // $scheme is a League\Url\Scheme object;
~~~

<p class="message-warning">If the submitted value is not a valid scheme an <code>InvalidArgumentException</code> will be thrown.</p>

To [output](/4.0/components/overview/#components-string-representations), [compare](/4.0/components/overview/#components-comparison) or [manipulate](/4.0/components/overview/#components-modification) the `Scheme` object you should refer to the component overview section.