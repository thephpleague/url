---
layout: default
title: The Path component
---

# The Path component

The library provides a `League\Url\Path` class to ease complex path manipulation.

## Path creation

### Using the default constructor

Just like any other component, a new `League\Url\Path` object can be instantiated using its default constructor.

~~~php
use League\Url\Path;

$absolute_path = new Path('/hello/world');
echo $absolute_path; //display '/hello/world'

$relative_path = new Path('hello/world');
echo $relative_path; //display 'hello/world'

$end_slash = new Path('hello/world/');
echo $end_slash; //display 'hello/world/'
~~~

<p class="message-warning">If the submitted value is not a valid path an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a League\Url\Url object

~~~php
use League\Url\Url;

$url  = Url::createFromUrl('http://url.thephpleague.com/path/to/here');
$path = $url->path; // $path is a League\Url\Path object;
~~~

### Using a named constructor

A path is a collection of segment delimited by the path delimiter `/`. So it is possible to create a `Path` object using a collection of segments with the `Path::createFromArray` method.

The method expects at most 2 arguments:

- The first required argument must be a collection of segments (an `array` or a `Traversable` object)
- The second optional argument, a `Url\Path` constant, tells whether this is a rootless path or not:
    - `Path::IS_ABSOLUTE`: the created object will represent an absolute path;
    - `Path::IS_RELATIVE`: the created object will represent a rootless path;

~~~php
use League\Url\Path;

$relative_path =  Path::createFromArray(['shop', 'example', 'com']);
echo $relative_path; //display 'shop/example/com'

$absolute_path = Path::createFromArray(['shop', 'example', 'com'], Path::IS_ABSOLUTE);
echo $absolute_path; //display '/shop/example/com'

$end_slash = Path::createFromArray(['shop', 'example', 'com', ''], Path::IS_ABSOLUTE);
echo $end_slash; //display '/shop/example/com/'
~~~

<p class="message-info">To force the end slash when using the <code>Path::createFromArray</code> method you need to add an empty string as the last member of the submitted array.</p>

## Path type

### Absolute or relative path

A path is considered absolute only if it starts with the path delimiter `/`, otherwise it is considered as being relative or rootless. At any given time you can test your path status using the `Path::isAbsolute` method.

~~~php
use League\Url\Path;

$relative_path = Path::createFromArray(['bar', '', 'baz']);
echo $relative_path; //displays 'bar//baz'
$relative_path->isAbsolute(); //return false;

$absolute_path = Path::createFromArray(['bar', '', 'baz'], Path::IS_ABSOLUTE);
echo $absolute_path; //displays '/bar//baz'
$absolute_path->isAbsolute(); //return true;
~~~

## Path representations

### String representation

Basic path representations is done using the following methods:

~~~php
use League\Url\Path;

$path = new Path('/path/to the/sky');
$path->__toString();      //return '/path/to%20the/sky'
$path->getUriComponent(); //return '/path/to%20the/sky'
~~~

### Array representation

A path can be represented as an array of its internal segments. Through the use of the `Path::toArray` method the class returns the object array representations.

<p class="message-info">A path ending with a slash will have an empty string as the last member of its array representation.</p>

<p class="message-warning">Once in array representation you can not distinguish a relative from a absolute path</p>

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->toArray(); //return ['path', 'to', 'the', 'sky'];

$absolute_path = new Path('/path/to/the/sky/');
$absolute_path->toArray(); //return ['path', 'to', 'the', 'sky', ''];

$relative_path = new Path('path/to/the/sky/');
$relative_path->toArray(); //return ['path', 'to', 'the', 'sky', ''];
~~~

## Accessing Path content

### Countable and IteratorAggregate

The class provides several methods to works with its segments. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of segments and use the `foreach` construct to iterate overs them.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
count($path); //return 4
foreach ($path as $offset => $segment) {
    //do something meaningful here
}
~~~

### Segment offsets

If you are interested in getting all the segments offsets you can do so using the `Path::offsets` method like shown below:

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->offsets();        //return [0, 1, 2, 3];
$path->offsets('sky');   //return [3];
$path->offsets('gweta'); //return [];
~~~

The method returns an array containing all the segments offsets. If you supply an argument, only the offsets whose segment value equals the argument are returned.

To know If an offset exists before using it you can use the `Path::hasOffset` method which returns `true` or `false` depending on the presence or absence of the submitted `$offset` in the current object.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->hasOffset(2);  //return true
$path->hasOffset(23); //return false
~~~

### Segment content

If you are only interested in a given segment you can access it directly using the `Path::getSegment` method as show below:

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getSegment(0);         //return 'path'
$path->getSegment(23);        //return null
$path->getSegment(23, 'now'); //return 'now'
~~~

The method returns the value of a specific offset. If the offset does not exists it will return the value specified by the optional second argument or `null`.

### The basename

To ease working with path you can get the trailing segment of a path by using the `Path::getBasename` method, this method takes no argument. If the segment ends with an extension, it will be included in the output.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); //return 'sky'

$alt_path = new Path('path/to/the/sky.html');
$alt_path->getBasename(); //return 'sky.html'
~~~

### The basename extension

If you are only interested in getting the basename extension, you can directly call the `Path::getExtension` method. This method, which takes no argument, returns the trailing segment extension as a string if present or an empty string. The leading `.` delimiter is removed from the method output.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); //return ''

$path = new Path('/path/to/file.csv');
$path->getExtension(); //return 'csv';
~~~

### The dirname

Conversely, you can get the path dirname by using the `Path::getDirname` method, this method takes no argument and works like PHP's `dirname` function.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky.txt');
$path->getExtension(); //return 'txt'
$path->getBasename();  //return 'sky.txt'
$path->getDirname();   //return '/path/to/the'
~~~

### Trailing delimiter

The `Path` object can tell you whether the current path ends with a delimiter or not using the `Path::hasTrailingDelimiter` method. This method takes no argument and return a boolean.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky.txt');
$path->hasTrailingDelimiter(); //return false

$altPath = new Path('/path/');
$altPath->hasTrailingDelimiter(); //return true
~~~

## Path normalization

<p class="message-notice">If the modifications do not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> exception is thrown.</p>

Out of the box, the `Path` object operates a number of non destructive normalizations. For instance, the path is correctly URL encoded against the RFC3986 rules.

### Removing dot segments

To remove dot segment as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-6) you need to explicitly call the `Path::withoutDotSegments` method as the result can be destructive. The method takes no argument and returns a new `Path` object which represents the current object normalized.

~~~php
use League\Url\Path;

$raw_path       = new Path('path/to/./the/../the/sky%7bfoo%7d');
$normalize_path = $raw_path->withoutDotSegments();
echo $raw_path;           //displays 'path/to/./the/../the/sky%7bfoo%7d'
echo $normalize_path;     //displays 'path/to/the/sky%7Bfoo%7D'
$alt->sameValueAs($path); //return false;
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url::withoutDotSegments</code> method</p>

### Removing empty segments

Sometimes your path may contain multiple adjacent delimiters. Since removing them may result in a semantically different URL, this normalization can not be applied by default. To remove adjacent delimiters you can call the `Path::withoutEmptySegments` method which convert you path as described below:

~~~php
use League\Url\Path;

$raw_path       = new Path("path////to/the/sky//");
$normalize_path = $raw_path->withoutEmptySegments();
echo $raw_path;           //displays 'path////to/the/sky//'
echo $normalize_path;     //displays 'path/to/the/sky/'
$alt->sameValueAs($path); //return false;
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url::withoutEmptySegments</code> method</p>

## Modifying Path

### Path extension manipulation

You can easily change or remove the extension from the path basename using the `Path::withExtension` method.

<p class="message-info">No update will be made if the <code>basename</code> is empty</p>

<p class="message-warning">This method will throw an <code>InvalidArgumentException</code> exception if the extension contains the path delimiter.</p>

~~~php
use League\Url\Path;

$path    = new Path('/path/to/the/sky');
$newPath = $path->withExtension('.csv');
echo $newPath->getExtension(); //displays csv;
echo $path->getExtension();    //displays '';
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url::withExtension</code> method</p>

### Append segments

To append segments to the current object you need to use the `Path::append` method. This method accept a single argument which represents the data to be appended. This data can be a string, an object which implements the `__toString` method or another `Path` object:

~~~php
use League\Url\Path;

$path    = new Path();
$newPath = $path->append(new Path('path'))->append('to/the/sky');
$newPath->__toString(); //return path/to/the/sky
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url::appendPath</code> method</p>

### Prepend segments

To prepend segments to the current path you need to use the `Path::prepend` method. This method accept a single argument which represents the data to be prepended. This data can be a string, an object which implements the `__toString` method or another `Path` object:

~~~php
use League\Url\Path;

$path    = new Path();
$newPath = $path->prepend(new Path('sky'))->prepend(new Path('path/to/the'));
$newPath->__toString(); //return path/to/the/sky
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url::prependPath</code> method</p>

### Replace segments

To replace a segment you must use the `Path::replace` method with the following arguments:

- `$offset` which represents the segment offset to remove if it exists.
- `$data` which represents the data to be inject.  This data can be a string, an object which implements the `__toString` method or another `Path` object.

~~~php
use League\Url\Path;

$path    = new Path('/foo/example/com');
$newPath = $path->replace(0, new Path('bar/baz'));
$Path->__toString(); //return /bar/baz/example/com
~~~

<p class="message-notice">if the specified offset does not exists, no modification is performed and the current object is returned.</p>

<p class="message-notice">This method is used by the <code>League\Url\Url::replaceSegment</code> method</p>

### Remove segments

To remove segments from the current object and returns a new `Path` object without them you must use the `Path::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$newPath = $path->without([0, 1]);
$newPath->__toString(); //return '/the/sky'
~~~

Or a callable that will select the list of offsets to remove.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$newPath = $path->without(function ($value) {
	return $value < 3;
});
echo $newPath; //displays '/sky';
~~~

<p class="message-notice">if the specified offset does not exists, no modification is performed and the current object is returned.</p>

<p class="message-notice">This method is used by the <code>League\Url\Url::withoutSegments</code> method</p>

### Filter segments

You can filter the `Path` object using the `Path::filter` method.

The first parameter must be a `callable`

~~~php
use League\Url\Path;

$path    = new Path('/foo/bar/yolo/');
$newPath = $path->filter(function ($value) {
	return ! empty($value);
});
echo $newPath; //displays '/foo/bar/yolo'
~~~

By specifying the second argument flag you can change how filtering is done:

- use `Path::FILTER_USE_VALUE` to filter according to the segment value;
- use `Path::FILTER_USE_KEY` to filter according to the segment offset;

By default, if no flag is specified the method will use the `Path::FILTER_USE_VALUE` flag.

~~~php
use League\Url\Path;

$path    = new Path('/foo/bar/yolo/');
$newPath = $query->filter(function ($value) {
	return 1 != $value;
}, Path::FILTER_USE_KEY);
echo $newPath; //displays '/foo/yolo'
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url::filterPath</code> method</p>