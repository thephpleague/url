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

### Path::getKeys()

Returns the keys of the Path object. If an argument is supplied to the method. Only the keys whose value equals the argument are returned.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/path');
$arr = $path->getKeys(); returns //  [0, 1, 2, 3];
$arr = $path->getKeys('path'); returns // [0, 3];
~~~

### Path::getData($key, $default = null)

Returns the value of a specific offset. If the offset does not exists it will return the value specified by the `$default` argument

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getData(0); //returns 'path'
$path->getData(23); //returns null
$path->getData(23, 'now'); //returns 'now'
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
$newPath = $path->appendWith('path')->append('to/the/sky', 'path');
$newPath->__toString(); //returns path/to/the/sky
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
$newPath = $path->prependWith('sky')->prependWith('path/to/the', 'sky');
$newPath->__toString(); //returns path/to/the/sky
~~~

### Path::replaceWith($data, $key)

Replace a path segment whose offset equals `$key` with the value given in the first argument `$data`.

- The `$data` argument which represents the data to be appended can be:
	- a string representation of a Pathname.
	- another `Path` interface
	- an object with the `__toString` method.

~~~php

use League\Url\Path;

$Path = new Path('/foo/example/com');
$newPath = $Path->replaceWith('bar/baz', 0);
$Path->__toString(); //returns /bar/baz/example/com
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
$Path->__toString(); //returns toto/com
~~~

### Path::normalize()

Normalize a `Path` object by removing dot segment as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-6). The method which takes no arguments returns a new `Path` object which represents the current object normalized.

~~~php

use League\Url\Path;

$path = new Path('path/to/./the/../the/sky%7bfoo%7d');
$alt  = $path->normalize();
echo $path; // displays 'path/to/./the/../the/sky%7bfoo%7d'
echo $alt; // displays 'path/to/the/sky%7Bfoo%7D'
$alt->sameValueAs($path); return false;
~~~
