---
layout: default
title: Data URIs
---

# Data URI

To ease working with Data URIs, the library comes bundle with a URI specific Data class. This class follows [RFC2397](http://tools.ietf.org/html/rfc2397)

## Instantiation

### Using the default named constructors

Just like with the others URI object you can use the `createFromString` and `createFromComponents` named constructors to instantiate a new Data object.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
echo $uri; //returns 'data:text/plain;charset=us-ascii,Hello%20World%21'

DataUri::createFromComponents(parse_url('http://www.example.com'));
//will throw an InvalidArgumentException
~~~

### Instantiating using a file path

Because data URI represents files you can also instantiate a new data URI object from a file path using the `createFromPath` named constructor

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

In addition to all the methods and properties exposes by all URI objects, The data URI class exposes the following specific methods:

- `getMimeType`: This method returns the Data URI current mimetype;
- `getParameters`: This method returns the parameters associated with the mediatype;
- `getData`: This methods returns the encoded data contained is the Data URI;

Each of these methods return a string. This string can be empty if the data where no supplied when constructing the URI.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
echo $uri->getMimetype(); //returns 'text/plain'
echo $uri->getParameters(); //returns 'charset=us-ascii'
echo $uri->getData(); //returns 'Hello%20World%21'
~~~

### Does the URI represents a binary data ?

To tell whether the data URI represents some binary data you can call the `isBinaryData` method. This method which returns a boolean will return `true` if the data is in a binary state. The binary state is checked on instantiation. Invalid binary dataURI will throw an `InvalidArgumentException` exception on initiation.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/png/image.png');
$uri->isBinaryData(); //returns true
$altUri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
$altUri->isBinaryData(); //returns false
~~~

## Manipulation

The data URI class is an immutable object everytime you manipulate the object a new object is returned with the modified value if needed.

### Update the Data URI parameters

Since we are dealing with a data and not just a URI, the only property that can be easily modified are its optional parameters.

To set new parameters you should use the `withParameters` method:

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
$newUri = $uri->withParameters('charset=utf-8');
echo $newUri; //returns 'data:text/plain;charset=utf-8,Hello%20World%21'
~~~

<p class="message-notice">Of note the data should be urlencoded if needed.</p>

### Transcode the data between its binary and ascii representation

Another manipulation is to transcode the data from ASCII to is base64 encoded (or binary) version. If no conversion is possible the former object is returned otherwise a new valid data uri object is created.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
$uri->isBinaryData(); // return false;
$newUri = $uri->dataToBinary();
$newUri->isBinaryData(); //return true;
$newUri->dataToAscii()->sameValueAs($uri); //return true;
~~~

## Saving the DataURI

Since a data URI is a file per se it is possible to save it to a specified path using the dedicated `save` method. This method accepts two parameters:

- the file path;
- the open mode (à la PHP `fopen`);

By default the open mode is set to `w`. If for any reason the file is not accessible a `RuntimeException` will be thrown.

The method returns the `SplFileObject` object used to save the data-uri data for further analysis/manipulation if you want.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/file.png');
$file = $uri->save('path/where/to/save/my/image.png');
//$file is a SplFileObject which point to the newly created file;
~~~
