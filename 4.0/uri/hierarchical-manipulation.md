---
layout: default
title: Manipulating Hierarchical URI
---

# Modifying Hierarchical URIs

Hierarchical URI like HTTP URL or FTP URLs actively uses the host components and a hierarchical path. The library exposes for these specific URI more modifying methods to help you manipulate them.

<p class="message-notice">If the modifications do not alter the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">The method may throw an <code>InvalidArgumentException</code> if the resulting URI is not valid for a scheme specific URI.</p>

## Generating a relative URI

A Hierarchical URI object provides the mean for relativizing an URI according the a referenced base URI.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$baseUri  = HttpUri::createFromString("http://www.example.com/this/is/a/long/uri/");
$childUri = HttpUri::createFromString("http://www.example.com/short#~toto");
echo $baseUri->relativize($childUri); //displays "../short#~toto"
~~~

<p class="message-notice">If you try to relativize two Uri object which do not share the same scheme. No normalization will occur and the submitted URI object will be return unchanged.</p>

~~~php
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Schemes\Http as WsUri;

$uri = HttpUri::createFromString("hTTp://www.ExAmPLE.com:80/hello/./wor ld?who=f+3#title");
$newUri = $uri->relativize(WsUri::createFromString("./p#~toto"));
echo $newUri; //displays "./p#~toto"
~~~

## Modifying URI path segments

### Append path segments

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->appendPath("/foo/bar");
echo $newUri; //display "http://www.example.com/path/to/the/sky.php/foo/bar"
~~~

`Uri::appendPath` is a proxy to simplify the use of [HierarchicalPath::append](/4.0/components/hierarchical-path/#append-segments) on a `Url` object.

### Prepend path segments

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->prependPath("/foo/bar");
echo $newUri; //display "http://www.example.com/foo/bar/path/to/the/sky.php"
~~~

`Uri::prependPath` is a proxy to simplify the use of [HierarchicalPath::prepend](/4.0/components/hierarchical-path/#prepend-segments) on a `Url` object.

### Replace a path segment

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->replaceSegment(0, "/foo/bar");
echo $newUri; //display "http://www.example.com/foo/bar/to/the/sky.php"
~~~

`Uri::replaceSegment` is a proxy to simplify the use of [HierarchicalPath::replace](/4.0/components/hierarchical-path/#replace-segments) on a `Url` object.

### Remove path segments

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->withoutSegments([0, 1]);
echo $newUri; //display "http://www.example.com/the/sky.php"
~~~

`Uri::withoutSegments` is a proxy to simplify the use of [HierarchicalPath::without](/4.0/components/hierarchical-path/#remove-segments) on a `Url` object.

### Filter the path

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->filterPath(function ($segment) {
    return strpos($segment, "t") === false;
});
echo $newUri; //display "http://www.example.com/sky.php"
~~~

`Uri::filterPath` is a proxy to simplify the use of [HierarchicalPath::filter](/4.0/components/hierarchical-path/#filter-segments) on a `Url` object.

### Remove dot segments

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/../to/the/./sky/");
$newUri = $uri->normalize();
echo $newUri; //display "http://www.example.com/to/the/sky/"
~~~

`Uri::normalize` is a proxy to simplify the use of [HierarchicalPath::normalize](/4.0/components/hierarchical-path/#removing-dot-segments) on a `Url` object.

### Remove internal empty segments

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com///path//to/the////sky//");
$newUri = $uri->withoutEmptySegments();
echo $newUri; //display "http://www.example.com/path/to/the/sky/"
~~~

`Uri::withoutEmptySegments` is a proxy to simplify the use of [HierarchicalPath::withoutEmptySegments](/4.0/components/hierarchical-path/#removing-empty-segments) on a `Url` object.

### Add a trailing slash

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->withTrailingSlash();
echo $newUri; //display "http://www.example.com/path/to/the/sky.php/"
~~~

`Uri::withTrailingSlash` is a proxy to simplify the use of [HierarchicalPath::withTrailingSlash](/4.0/components/hierarchical-path/#path-trailing-slash-manipulation) on a `Url` object.

### Remove the trailing slash

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/");
$newUri = $uri->withoutTrailingSlash();
echo $newUri; //display "http://www.example.com"
~~~

`Uri::withoutTrailingSlash` is a proxy to simplify the use of [HierarchicalPath::withoutTrailingSlash](/4.0/components/hierarchical-path/#path-trailing-slash-manipulation) on a `Url` object.

### Update the path extension

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://www.example.com/path/to/the/sky.php");
$newUri = $uri->withExtension("csv");
echo $newUri; //display "http://www.example.com/path/to/the/sky.csv"
~~~

`Uri::withExtension` is a proxy to simplify the use of [HierarchicalPath::withExtension](/4.0/components/hierarchical-path/#path-extension-manipulation) on a `Url` object.
