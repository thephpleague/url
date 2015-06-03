---
layout: default
title: The Path component
---

# The Path component

The library provides a `League\Url\Path` class to ease complex path manipulation.

## Path creation

### Using the default constructor

Just like any other component, a new `League\Url\Path` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

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
- The second optional argument, a PHP constants, tells whether this is rootless path or not:
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

A path is considered absolute only if it stats with the path delimiter `/`, otherwise it is considered as being relative. At any given time you can test your path status using the `Path::isAbsolute` method.

~~~php
use League\Url\Path;

$relative_path = Path::createFromArray(['bar', '', 'baz']);
echo $relative_path; //displays 'bar//baz'
$relative_path->isAbsolute(); // returns false;

$absolute_path = Path::createFromArray(['bar', '', 'baz'], Path::IS_ABSOLUTE);
echo $absolute_path; //displays '/bar//baz'
$absolute_path->isAbsolute(); // returns true;
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

The path string representation may vary depending on its context, when used standalone or in a URL string. The `Path::getUriComponent` enable these other representations using two optional arguments:

- the `$context` argument selects the context using two constants `Path::PATH_AS_STANDALONE` and `Path::PATH_AS_URLPART`
- the `$has_authority_part` argument informs the path about the presence or absence of an authority part in the URL using a boolean when `Path::PATH_AS_URLPART` is used.

By default `context` is set to `Path::PATH_AS_STANDALONE` and `has_authority_part` to `false`

~~~php
use League\Url\Path;

$path = new Path('path/to the/sky');
$path->getUriComponent(); //return 'path/to%20the/sky'
$path->getUriComponent(Path::PATH_AS_URLPART, true); //return '/path/to%20the/sky'

$anotherPath = new Path();
$anotherPath->getUriComponent(Path::PATH_AS_URLPART, true); //return ''
~~~

### Array representation

A path can be represented as an array of its internal segments. Through the use of the `Path::toArray` method the class returns the object array representations.

<p class="message-info">A path ending with a slash will have an empty string as the last member of its array representation.</p>

<p class="message-warning">Once in array representation you can not distinguish a relative from a absolute path</p>

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->toArray(); // returns ['path', 'to', 'the', 'sky'];

$absolute_path = new Path('/path/to/the/sky/');
$absolute_path->toArray(); // returns ['path', 'to', 'the', 'sky', ''];

$relative_path = new Path('path/to/the/sky/');
$relative_path->toArray(); // returns ['path', 'to', 'the', 'sky', ''];
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

If you are interested in getting all the segments offsets you can do so using the `Path::offsets` method like show below:

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->offsets(); // returns  [0, 1, 2, 3];
$path->offsets('sky'); // returns [3];
$path->offsets('gweta'); // returns [];
~~~

The methods returns all the segments offsets, but if you supply an argument, only the offsets whose segment value equals the argument are returned.

If you want to be sure that an offset exists before using it you can do so using the `Path::hasOffset` method which returns `true` if the submitted `$offset` exists in the current object.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->hasOffset(2); // returns true
$path->hasOffset(23); // returns false
~~~

### Segment content

If you are only interested in a given segment you can access it directly using the `Path::getSegment` method as show below:

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getSegment(0); //returns 'path'
$path->getSegment(23); //returns null
$path->getSegment(23, 'now'); //returns 'now'
~~~

The method returns the value of a specific offset. If the offset does not exists it will return the value specified by the second `$default` argument.

### The basename

To ease working with path you can get the trailing segment of a Path object by using the `Path::getBasename` method, this method takes no argument. If the segment ends in suffix, the suffix is included.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); // returns 'sky'

$alt_path = new Path('path/to/the/sky.html');
$alt_path->getBasename(); // returns 'sky.html'
~~~

### The basename extension

If you are only interested in getting the basename extension, you can directly call the `Path::getBasename` method, this method takes no argument. The method return the trailign segment extension as a string if present or an empty string. The leading `.` delimiter is removed from the method output.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); // returns ''

$path = new Path('/path/to/file.csv');
$path->getExtension(); // return 'csv';
~~~

### The dirname

Conversely, you can get the path dirname by using the `Path::getDirname` method, this method takes no argument. This methods works like PHP's `dirname` function.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$path->getBasename(); // returns 'sky'
$path->getDirname(); // returns '/path/to/the'
~~~

## Path normalization

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

Out of the box, the `Path` object operates a number of normalization to the submitted path. All these normalization are non destructive, for instance, the path is correctly URL encoded against the RFC rules.

### Removing dot segments

To remove dot segment as per as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-6) you need to explicitly call the `Path::withoutDotSegments` method as the result can be destructive. The method takes no arguments and returns a new `Path` object which represents the current object normalized.

~~~php
use League\Url\Path;

$raw_path       = new Path('path/to/./the/../the/sky%7bfoo%7d');
$normalize_path = $raw_path->withoutDotSegments();
echo $raw_path;           //displays 'path/to/./the/../the/sky%7bfoo%7d'
echo $normalize_path;     //displays 'path/to/the/sky%7Bfoo%7D'
$alt->sameValueAs($path); //return false;
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::withoutDotSegments</code></p>

### Removing empty segments

Sometimes your path may contain multiple adjacent delimiters. Since removing them may result in a semantically different URL, this normalization can not be applied by default. To remove adjacent delimiters you can call the `Path::withoutEmptySegments` method which convert you path as described below:


~~~php
use League\Url\Path;

$raw_path       = new Path('path////to/the/sky//');
$normalize_path = $raw_path->withoutEmptySegments();
echo $raw_path;           //displays 'path////to/the/sky//'
echo $normalize_path;     //displays 'path/to/the/sky/'
$alt->sameValueAs($path); //return false;
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::withoutEmptySegments</code></p>

## Modifying Path

### Path extension manipulation

You can easily change or remove the extension from the path basename using the `Path::withExtension` method.

<p class="message-info">No update will be made is the <code>basename</code> is empty</p>

<p class="message-warning">This method will throw an <code>InvalidArgumentException</code> If the extension contains a slash character.</p>

~~~php
use League\Url\Path;

$path    = new Path('/path/to/the/sky');
$newPath = $path->withExtension('.csv');
echo $newPath; //displays /path/to/the/sky.csv;
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::withExtension</code></p>

### Append segments

To append segments to the current object you need to use the `Path::append` method. This method accept a single `$data` argument which represents the data to be appended. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Path;

$path    = new Path();
$newPath = $path->append('path')->append('to/the/sky');
$newPath->__toString(); //returns path/to/the/sky
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::appendPath</code></p>

### Prepend segments

To prepend segments to the current path you need to use the `Path::prepend` method. This method accept a single `$data` argument which represents the data to be prepended. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Path;

$path    = new Path();
$newPath = $path->prepend(new Path('sky'))->prepend(new Path('path/to/the'));
$newPath->__toString(); // returns path/to/the/sky
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::prependPath</code></p>

### Replace segments

To replace a segment with your own data, you must use the `Path::replace` method with the following arguments:

- `$offset` which represents the label's offset to remove if it exists.
- `$data` which represents the data to be inject. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Path;

$path    = new Path('/foo/example/com');
$newPath = $path->replace(0, new Path('bar/baz'));
$Path->__toString(); //returns /bar/baz/example/com
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::replaceSegment</code></p>

### Remove segments

To remove segments from the current object and returns a new `Path` object without them you must use the `Path::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Url\Path;

$path = new Path('/path/to/the/sky');
$newPath = $path->without([0, 1]);
$newPath->__toString(); //returns '/the/sky'
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

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::withoutSegments</code></p>

### Filter segments

Another way to select segments from the path object is to filter them.

You can filter the path according to its segments using the `Path::filter` method.

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

By default, if no flag is specified the method will filter by value.

~~~php
use League\Url\Path;

$path    = new Path('/foo/bar/yolo/');
$newPath = $query->filter(function ($value) {
	return 1 != $value;
}, Path::FILTER_USE_KEY);
echo $newPath; //displays '/foo/yolo'
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::filterPath</code></p>