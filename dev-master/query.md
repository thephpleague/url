---
layout: default
title: The Query Component
---

# The Query component

This component is manage throught the `Query` class which implements the following interfaces:

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\Component`
- `League\Url\Interfaces\Query`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

<p class="message-info">On output, the query string is encoded following the <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a></p>

## The Query class

### Query::__construct($data = null)

The `$data` argument which represents the data to be appended can be:

- a string representation of a query.
- another `Query` interface.
- an object with the `__toString` method.

~~~php
use League\Url\Query;

$query = new Query('?foo=bar&baz=nitro');
$alt = new Query($query);
$alt->sameValueAs($query); //returns true
~~~

### Query::createFromArray

To ease instantiation you can use this named constructor to generate a new `Query` object from an `array` or a `Traversable` object.

~~~php
use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'single' => '', 'toto' => 'baz']);
echo $query; //returns 'foo=bar&single&toto=baz'
~~~

### Query::toArray()

Returns an array representation of the query string

~~~php
use League\Url\Query;

$query = new Query('foo=bar&baz=nitro');
$arr = $query->toArray(); // returns  ['foo' => 'bar', 'baz' => 'nitro', ];
~~~

### Query::getParameter($offset, $default = null)

Returns the value of a specific key. If the key does not exists it will return the value specified by the `$default` argument

~~~php
use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$query->getParameter('baz'); //returns 'toto'
$query->getParameter('change'); //returns null
$query->getParameter('change', 'now'); //returns 'now'
~~~

### Query::offsets($parameter = null)

Returns the offsets associated to the current query string. If an argument is supplied to the method, only the offsets whose values equals the argument are returned. Otherwise an empty array is returned.

~~~php
use League\Url\Query;

$query = new Query('foo=bar&baz=nitro&change=nitro');
$query->offsets(); // returns  ['foo', 'baz', 'chance'];
$query->offsets('nitro'); // returns ['baz', 'chance'];
$query->offsets('gweta'); // returns [];
~~~

### Query::hasOffset($offset)

Returns `true` if the submitted `$offset` exists in the current object.

~~~php
use League\Url\Query;

$query = new Query('foo=bar&baz=nitro&change=nitro');
$query->hasOffset('foo'); // returns true
$query->hasOffset('gweta'); // returns false
~~~

### Query::merge(Query $query)

The single `$query` argument must implement the Query interface. The data will be merge between both query object and a new instance of the Query object will be returned with the merge data. Of note, this method only adds or updates the values of the query string. You can not remove value from the query by using this method.

~~~php
use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$alt->get(); //returns foo=bar&baz=toto
$new = $alt->merge('foo=jane');
$new->get(); //returns foo=jane&baz=toto
~~~

### Query::without(array $offsets = [])

Remove parameter from the current object and returns a new `Query` object without the removed parameters.

The `$offsets` argument is an array containing a list of offsets to remove.

~~~php

use League\Url\Path;

$host = new Path('/path/to/the/sky');
$host->without([0, 1]);
$host->__toString(); //returns '/the/sky'
~~~