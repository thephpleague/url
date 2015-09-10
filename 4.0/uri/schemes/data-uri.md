---
layout: default
title: Data URIs
---

# Data URI

To ease working with Data URIs, the library comes bundle with a URI specific Data class. This class follows [RFC2397](http://tools.ietf.org/html/rfc2397)

## Instantiation

In addition to the [defined named constructors](/4.0/uri/instantiation/#uri-instantiation), because data URI represents files, you can also instantiate a new data URI object from a file path using the `createFromPath` named constructor

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/png/image.png');
echo $uri; //returns 'data:image/png;charset=binary;base64,...'
//where '...' represent the base64 representation of the file
~~~

If the file is not readable or accessible an InvalidArgumentException exception will be thrown. The class uses PHP's `finfo` class to detect the required mediatype as defined in RFC2045.

## Validation

Even thout all URI properties are defined and accessible with the dataURI attempt to set the following component or URI part will result in the object throwing a `InvalidArgumentException` exception. As adding data to theses URI part will generate an invalid URI.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/png/image.png');
$uri->getHost(); //return '' an empty string
$uri->withHost('example.com'); //thrown an InvalidArgumentException
~~~

## Properties

The data URI class uses the [DataPath](/4.0/components/datauri-path/) class to represents its path. using PHP's magic `__get` method you can access the object path and get more information about the underlying file.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
echo $uri->path->getMimetype(); //returns 'text/plain'
echo $uri->path->getParameters(); //returns 'charset=us-ascii'
echo $uri->path->getData(); //returns 'Hello%20World%21'
$uri->path->isBinaryData(); //returns false
~~~