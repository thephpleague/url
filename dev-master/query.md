---
layout: default
title: The Query Component
---

# The Query component

This component is manage throught the `Host` class which implements the following interfaces:

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\Component`
- `League\Url\Interfaces\Query`

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

to ease instanciation you can use this named constructor to generate a new Query class from an `array` or a `Traversable` object.

~~~php

use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'single' => '', 'toto' => 'baz']);
echo $query; //returns 'foo=bar&single&toto=baz'
~~~

### Query::mergeWith($data)

The single `$data` can be:

- an `array`,
- a `Traversable` object
- a string representation of a query string.

<p class="message-info">When providing an <code>array</code> or a <code>Traversable</code> object. If the value associated to an offset equals <code>null</code>, the resulting key will be remove from the returned new query object.</p>

~~~php

use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$alt->get(); //returns foo=bar&baz=toto
$new = $alt->mergeWith('foo=jane');
$new->get(); //returns foo=jane&baz=toto
~~~

### Query::getData($key, $default = null)

Returns the value of a specific key. If the key does not exists it will return the value specified by the `$default` argument

~~~php

use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$query->getData('baz'); //returns 'toto'
$query->getData('change'); //returns null
$query->getData('change', 'now'); //returns 'now'
~~~

### Query::toArray()

Returns an array representation of the query string

~~~php

use League\Url\Query;

$query = new Query('foo=bar&baz=nitro');
$arr = $query->toArray(); returns //  ['foo' => 'bar', 'baz' => 'nitro', ];
~~~

### Query::getKeys($value = null)

Returns the keys of the Query object. If an argument is supplied to the method. Only the key whose value equals the argument are returned.

~~~php

use League\Url\Query;

$query = new Query('foo=bar&baz=nitro&change=nitro');
$arr = $query->getKeys(); returns //  ['foo', 'baz', 'chance'];
$arr = $query->getKeys('nitro'); returns // ['baz', 'chance'];
~~~
