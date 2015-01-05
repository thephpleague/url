---
layout: default
title: The Path component
---

# The Path component

This [URL multiple values component](/dev-master/component/#complex-components) is manage by implementing the following interfaces:

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\PathInterface`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

## The Path class

### Path::__construct($data = null)

The class constructor takes a single argument `$data` which can be:

- a string representation of a pathname.
- an `array`
- a `Traversable` object
- another `PathInterface` object

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$alt = new Path($path);
$alt->sameValueAs($path); //returns true
~~~

## The PathInterface

This interface extends the [ComponentInterface](/dev-master/component/#the-componentinterface) interface with the following methods.

### PathInterface::toArray()

Returns an array representation of the path string

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$arr = $path->toArray(); returns //  ['path', 'to', 'the', 'sky'];
~~~

### PathInterface::keys()

Returns the keys of the Path object. If an argument is supplied to the method. Only the keys whose value equals the argument are returned.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/path');
$arr = $path->keys(); returns //  [0, 1, 2, 3];
$arr = $path->keys('path'); returns // [0, 3];
~~~

### PathInterface::getSegment($offset, $default = null)

Returns the value of a specific offset. If the offset does not exists it will return the value specified by the `$default` argument

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getSegment(0); //returns 'path'
$path->getSegment(23); //returns null
$path->getSegment(23, 'now'); //returns 'now'
~~~

### PathInterface::setSegment($offset, $value)

Set a specific key from the object. `$offset` must be an integer between 0 and the total number of label. If `$value` is empty or equals `null`, the specified key will be deleted from the current object.

~~~php

use League\Url\Path;

$path = new Path();
count($path); // returns 0
$path->setSegment(0, 'bar');
$path->getSegment(0); //returns 'bar'
count($path); // returns 1
$path->setSegment(0, null); //returns null
count($path); // returns 0
$path->getSegment(0); //returns null
~~~

### PathInterface::append($data, $whence = null, $whence_index = null)

Append data to the component.

- The `$data` argument which represents the data to be appended can be:
    - a string representation of a path component;
    - an `array`
    - a `Traversable` object
- The `$whence` argument represents the label **after which** the data will be included
- The `$whence_index` represents the the index of the `$whence` argument if present multiple times

~~~php

use League\Url\Path;

$path = new Path();
$path->append('path');
$path->append('to/the/sky', 'path');
$path->__toString(); //returns path/to/the/sky
~~~

### PathInterface::prepend($data, $whence = null, $whence_index = null)

Prepend data to the component.

- The `$data` argument which represents the data to be prepended can be:
    - a string representation of a path component;
    - an `array`
    - a `Traversable` object
- The `$whence` argument represents the label **before which** the data will be included
- The `$whence_index` represents the the index of the `$whence` argument if present multiple times

~~~php

use League\Url\Path;

$path = new Path();
$path->prepend('sky');
$path->prepend('path/to/the', 'sky');
$path->__toString(); //returns path/to/the/sky
~~~

### PathInterface::remove($data)

Remove part of the `Path` component data. The method returns `true` if the `$data` has been successfully removed. The `$data` argument which represents the data to be prepended can be:
    - a string representation of a path component;
    - an `array`
    - a `Traversable` object

if `$data` is present multiple times in the Path object you must repeat your call to `PathInterface::remove` method as long as the method returns `true`.

~~~php

use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->remove('the');
$path->__toString(); //returns path/to/sky
~~~

### PathInterface::normalize()

Normalize a `PathInterface` object by removing dot segment as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-6). The method which takes no arguments returns a new `PathInterface` object which represents the current object normalized.

~~~php

use League\Url\Path;

$path = new Path('path/to/./the/../the/sky%7bfoo%7d');
$alt  = $path->normalize();
echo $path; // displays 'path/to/./the/../the/sky%7bfoo%7d'
echo $alt; // displays 'path/to/the/sky%7Bfoo%7D'
$alt->sameValueAs($path); return false;
~~~
