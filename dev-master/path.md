---
layout: default
title: The Path component
---

# The Path component

This component is manage throught the `Path` class which implements the following interfaces:

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\Component`
- `League\Url\Interfaces\Segment`
- `League\Url\Interfaces\Path`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

These interfaces provide methods to help normalize and modify the path in a predicable manner using the following methods:

## The Path class

### Path::__construct($data = null)

The class constructor takes a single argument `$data` which can be:

- a string representation of a Pathname.
- another `PathInterface` object
- an object with the `__toString` method.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$alt = new Path($path);
$alt->sameValueAs($path); //returns true
~~~

### Path::createFromArray($data, $is_absolute = false)

To ease instantiation you can use this named constructor to generate a new `Path` object from an `array` or a `Traversable` object.

If you want your path to be absolute you need to specify it using the `$is_absolute` argument.

~~~php

use League\Url\Path;

echo Path::createFromArray(['bar', '', 'baz'])->__toString(); //returns 'bar//baz'
echo Path::createFromArray(['bar', '', 'baz'], true)->__toString(); //returns '/bar//baz'
~~~

### Path::isAbsolute()

At any given time you can verify if the path you are currently manipulating is absolute using this method.

~~~php

use League\Url\Path;

$relative_path = Path::createFromArray(['bar', '', 'baz']);
$relative_path->isAbsolute(); // returns false;

$absolute_path  = Path::createFromArray(['bar', '', 'baz'], true);
$absolute_path->isAbsolute(); // returns true;
~~~

### Path::toArray()

Returns the `Path` object as an array of segments. If the path ends with a delimiter an empty segment is added.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$arr = $path->toArray(); // returns ['path', 'to', 'the', 'sky'];

$path = new Path('/path/to/the/sky');
$arr = $path->toArray(); // returns ['path', 'to', 'the', 'sky', ''];
~~~

### Path::normalize()

Normalize a `Path` object by removing dot segment as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-6). The method which takes no arguments returns a new `Path` object which represents the current object normalized.

~~~php

use League\Url\Path;

$raw_path = new Path('path/to/./the/../the/sky%7bfoo%7d');
$normalize_path  = $raw_path->normalize();
echo $raw_path;        // displays 'path/to/./the/../the/sky%7bfoo%7d'
echo $normalize_path;  // displays 'path/to/the/sky%7Bfoo%7D'
$alt->sameValueAs($path); return false;
~~~

### Path::getSegment($offset, $default = null)

Returns the value of a specific offset. If the offset does not exists it will return the value specified by the `$default` argument

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getSegment(0);         // returns 'path'
$path->getSegment(23);        // returns null
$path->getSegment(23, 'now'); // returns 'now'
~~~

### Path::getOffsets($segment = null)

Returns the keys of the `Path` object. If an argument is supplied to the method. Only the keys whose segment value equals the argument are returned.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/path');
$arr = $path->getOffsets();       // returns [0, 1, 2, 3];
$arr = $path->getOffsets('path'); // returns [0, 3];
~~~

### Path::hasOffset($offset)

Returns `true` if the submitted `$offset` exists in the current object.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/path');
$path->hasOffset(2); // returns true
$path->hasOffset(23); // returns false
~~~

### Path::getBasename()

Returns the trailing segment of the Path object. If the segment ends in suffix, the suffix is included.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); // returns 'sky'
~~~

### Path::getExtension()

Returns the trailing segment extension as a string if present, otherwise the method return an empty string. The leading dot delimiter is removed from the method output.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); // returns ''

$path = new Path('/path/to/file.csv');
$path->getExtension(); // return 'csv';
~~~

### Path::withExtension($extension)

Returns a new `Path` instance with the updated extension for the last segment.

- If the extension contains a slash character, this method will throw a `InvalidArgumentException`.
- If the `Path` object basename is empty, this method will throw a `LogicException`.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$new_path = $path->withExtension('.csv');
echo $new_path; // displays /path/to/the/sky.csv;
~~~

### Path::appendWith($data)

Append the current path with the given data and return a new Path instance.

The `$data` argument which represents the data to be appended can be:

- a string representation of a Pathname.
- another `PathInterface` object
- an object with the `__toString` method.

~~~php

use League\Url\Path;

$path = new Path();
$newPath = $path->appendWith('path')->appendWith('to/the/sky');
$newPath->__toString(); // returns path/to/the/sky
~~~

### Path::prependWith($data)

Prepend the current path with the given data and return a new Path instance.

The `$data` argument which represents the data to be appended can be:

- a string representation of a Pathname.
- another `PathInterface` object
- an object with the `__toString` method.

~~~php

use League\Url\Path;

$path = new Path();
$newPath = $path->prependWith('sky')->prependWith('path/to/the');
$newPath->__toString(); // returns path/to/the/sky
~~~

### Path::replaceWith($data, $offset)

Replace a path segment whose offset equals `$offset` with the value given in the first argument `$data`.

- The `$data` argument which represents the data to be appended can be:
	- a string representation of a Pathname.
	- another `Path` interface
	- an object with the `__toString` method.

~~~php

use League\Url\Path;

$Path = new Path('/foo/example/com');
$newPath = $Path->replaceWith('bar/baz', 0);
$Path->__toString(); // returns /bar/baz/example/com
~~~

### Path::without($data)

Remove full segments from the Path and return a new Path object without the removed segments.

- The `$data` argument which represents the data to be appended can be:
	- a string representation of a Pathname.
	- another `PathInterface` object
	- an object with the `__toString` method.

if `$data` is present multiple times in the Path object only the first occurrence found will be removed. You will have to repeat the operation as often as `$data` is present in the Path.

~~~php

use League\Url\Path;

$Path = new Path('toto/example/com');
$Path->without('example');
$Path->__toString(); // returns toto/com
~~~
