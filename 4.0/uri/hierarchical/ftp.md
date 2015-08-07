---
layout: default
title: Ftp URIs
---

# Ftp URI

To ease working with FTP URIs, the library comes bundle with a URI specific FTP class

## Validating a FTP URI

A FTP URI can not contains a query and or a fragment component.

<p class="message-notice">Adding contents to the fragment or query components throws an <code>RuntimeException</code> exception</p>

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=i');
$uri->withQuery('p=1'); //throw an InvalidArgumentException
~~~

## Detecting the URI typecode

According to [RFC1738]() a FTP Uri can optionnally inform about the URI typecode by prefixing its path component. The `Ftp::getTypecode` method enables retrieving such typecode.

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=i');
$uri->getTypecode(); //returns "i"
~~~

If no typecode is detected this method will return an empty string.

## Modifying the URI typecode

The FTP typecode information can also be modified using the `withTypecode` method. This methods returns a new URI object with the modified typecode. With the `withTypecode` method you can:

- suffix the path with a new typecode

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png');
$newUri = $uri->withTypecode('a');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png;type=a'
~~~

- update the already present typecode

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=a');
$newUri = $uri->withTypecode('d');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png;type=d'
~~~

- remove the current typecode by providing an empty string.

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=d');
$newUri = $uri->withTypecode('');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png'
~~~

<p class="message-warning">When modifying the typecode the class only validate the return string. Additional check should be done to ensure that the path is valid for a given typecode.</p>

## Detecting the file extension

Because of the presence of the typecode, The FTP class comes with a special `Ftp::getExtension` method which does take into account the file typecode if present.

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=d');
$uri->getExtension(); //returns 'png'
$uri->path->getExtension(); //returns 'png;type=d'
~~~

Conversely the class takes into account the typecode presence when updating the file extension.

~~~php
use League\Uri\Schemes\Ftp as FpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=d');
$newUri = $uri->withExtension('gif');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.gif;type=d'
~~~
