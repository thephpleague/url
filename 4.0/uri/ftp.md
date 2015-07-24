---
layout: default
title: Ftp URIs
---

# Ftp URI

To ease working with FTP URIs, the library comes bundle with a URI specific FTP class

## Validating a FTP URI

A FTP URI can not contains a query and or a fragment component. Any attempt to modify the empty query or fragment components attached to an FTP Uri object will throw an `InvalidArgumentException` exception

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

//don't forget to provide the $_SERVER array
$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png');
$newUri = $uri->withTypecode('a');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png;type=a'
~~~

- update the already present typecode

~~~php
use League\Uri\Schemes\Ftp as FpUri;

//don't forget to provide the $_SERVER array
$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=a');
$newUri = $uri->withTypecode('d');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png;type=d'
~~~

- remove the current typecode by providing an empty string.

~~~php
use League\Uri\Schemes\Ftp as FpUri;

//don't forget to provide the $_SERVER array
$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=d');
$newUri = $uri->withTypecode('');
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png'
~~~
