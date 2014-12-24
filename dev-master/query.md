---
layout: default
title: The Query Component
---

# The Query component

This [URL multiple values component](/components/overview/#complex-components) is manage by implementing the following interfaces:

- `ArrayAccess`
- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\QueryInterface`.

<p class="message-info">On output, the query string is encoded following the <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a></p>

## The Query class

### Query::__construct($data = null)

The class constructor takes a single argument `$data` which can be:

- a string representation of a query string.
- an `array`
- a `Traversable` object
- another `QueryInterface` object

~~~php

use League\Url\Query;

$query = new Query('?foo=bar&baz=nitro');
$alt = new Query($query);
$alt->sameValueAs($query); //returns true
~~~

## QueryInterface

This interface extends the [`ComponentInterface`](/dev-master/component/) by adding the following methods:

### QueryInterface::modify($data)

The method allow modifying the query. just like with the `QueryInterface::set` method, the single `$data` can be:

- an `array`,
- a `Traversable` object
- a string representation of a query string.

~~~php

use League\Url\Query;

$query = new Query();
$query->modify(['foo' => 'bar', 'baz' => 'toto']);
$query->get(); //returns foo=bar&baz=toto
$query->modify('foo=jane');
$query->get(); //returns foo=jane&baz=toto
~~~

### QueryInterface::getParameter($key, $default = null)

Returns the value if a specific key. If the key does not exists it will return the value specified by the `$default` argument

~~~php

use League\Url\Query;

$query = new Query();
$query->modify(['foo' => 'bar', 'baz' => 'toto']);
$query->getParameter('baz'); //returns 'toto'
$query->getParameter('change'); //returns null
$query->getParameter('change', 'now'); //returns 'now'
~~~

### QueryInterface::setParameter($key, $value)

Set a specific key from the object. `$key` must be a string. If `$value` is empty or equals `null`, the specified key will be deleted from the current object.

~~~php

use League\Url\Query;

$query = new Query();
count($query); // returns 0
$query->setParameter('foo', 'bar');
$query->getParameter('foo'); //returns 'bar'
count($query); // returns 1
$query->setParameter('foo', null); //returns null
count($query); // returns 0
$query->getParameter('foo'); //returns null
~~~

### QueryInterface::toArray()

Returns an array representation of the query string

~~~php

use League\Url\Query;

$query = new Query('foo=bar&baz=nitro');
$arr = $query->toArray(); returns //  ['foo' => 'bar', 'baz' => 'nitro', ];
~~~

### QueryInterface::keys()

Returns the keys of the Query object. If an argument is supplied to the method. Only the key whose value equals the argument are returned.

~~~php

use League\Url\Query;

$query = new Query('foo=bar&baz=nitro');
$query->setParameter('change', 'nitro');
$arr = $query->keys(); returns //  ['foo', 'baz', 'chance'];
$arr = $query->keys('nitro'); returns // ['baz', 'chance'];
~~~
