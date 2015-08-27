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
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=i');
$uri->withQuery('p=1'); //throw an InvalidArgumentException
~~~

## Detecting the URI typecode

According to [RFC1738]() a FTP Uri can optionnally inform about the URI typecode by suffixing its path component. The `Ftp::getTypecode` method enables retrieving such typecode.

To ease typecode manipulation the FTP Uri object exposes the typecode using constants:

- `League\Uri\Schemes\Ftp::TYPE_ASCII` : for text files;
- `League\Uri\Schemes\Ftp::TYPE_BINARY` : for binary files;
- `League\Uri\Schemes\Ftp::TYPE_DIRECTORY` : for directory path;
- `League\Uri\Schemes\Ftp::TYPE_NONE` : when the typecode is not present;

~~~php
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=i');
$uri->getTypecode(); //returns FtpUri::TYPE_BINARY
~~~

If no typecode is detected or is not present the method return `League\Uri\Schemes\Ftp::TYPE_NONE`.

## Modifying the URI typecode

The FTP typecode information can also be modified using the `withTypecode` method. This methods returns a new URI object with the modified typecode. With the `withTypecode` method you can:

- suffix the path with a new typecode

~~~php
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png');
$newUri = $uri->withTypecode(FtpUri::TYPE_ASCII);
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png;type=a'
~~~

- update the already present typecode

~~~php
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=a');
$newUri = $uri->withTypecode(FtpUri::TYPE_DIRECTORY);
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png;type=d'
~~~

- remove the current typecode by providing an empty string.

~~~php
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString('ftp://thephpleague.com/path/to/image.png;type=d');
$newUri = $uri->withTypecode(FtpUri::TYPE_NONE);
$newUri->__toString(); //returns 'ftp://thephpleague.com/path/to/image.png'
~~~

<p class="message-warning">When modifying the typecode the class only validate the return string. Additional check should be done to ensure that the path is valid for a given typecode.</p>
