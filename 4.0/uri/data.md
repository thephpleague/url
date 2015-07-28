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

DataUri::createFromComponents(DataUri::parse('http://www.example.com'));
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

## Properties

The data URI class exposes the following methods:

- `getMimetype`: This method returns the Data URI current mimetype;
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

The data URI class is an immutable object everytime you manipulate the object a new object is returned with the modified value if needed. Since we are dealing with a data and not just a URI, the only property that can be modified is its parameters.

The parameters are manipulated using the `Parameters` object which is similar to the [Query](/4.0/components/query/) object. It supports the same properties and methods. The only difference between both classes is the value of their respective default separators and delimiters properties:

- The `Parameters` class default separator is `;`;
- The `Parameters` class default delimiter is `;`;

You can access the parameter object using PHP magic getter methods:

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/png/image.png');
$parameters = $uri->parameters;

count($parameters); //returns 2;
echo $parameters; //return 'charset=binary;base64'
~~~

To modify the parameters you can:

- add or update them with the `mergeParameters` method

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/png/image.png');
$newUri = $uri->mergeParameters(['yolo' => 'bar']);
$newUri->getParameters(); //returns 'charset=binary;base64;yolo=bar'
~~~

- remove somes parameters with the `withoutParameters` method by specifying the parameters keys

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/png/image.png');
$newUri = $uri->mergeParameters(['charset']);
$newUri->getParameters(); //returns 'base64'
~~~

or completly remove/set the parameters with the `withParameters` method:

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromString('data:text/plain;charset=us-ascii,Hello%20World%21');
$newUri = $uri->withParameters('charset=utf-8');
echo $newUri; //returns 'data:text/plain;charset=utf-8,Hello%20World%21'
~~~

<p class="message-warning">Even though the binary flag is present in the Parameters object you can not alter its value with the above methods. Doing so will throw an <code>InvalidArgumentException</code> as this would modified the data nature.</p>

## Saving the DataURI

Since a data URI is a file per se it is possible to save it to a specified path using the dedicated `save` method. This method accepts two parameters:

- the file path;
- the open mode (à la PHP `fopen`);

By default the open mode is set to `w`. If for any reason the file is not accessible a `RuntimeException` will be thrown.

When successfully save, the method returns the `SplFileObject` object used to save the data-uri data.

~~~php
use League\Uri\Schemes\Data as DataUri;

$uri = DataUri::createFromPath('path/to/my/file.png');
$file = $uri->save('path/where/to/save/my/image.png');
//$file is a SplFileObject which point to the newly created file;
~~~

