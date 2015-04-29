---
layout: default
title: URL Components
---

# URL components

An URL string is composed of up to 8 components which are in order of appearance:

- Scheme;
- User;
- Pass;
- Host;
- Port;
- Path;
- Query;
- Fragment;

The `League\Url` library provides an access to each URL components via a set of interfaces and classes. These classes can all be use independently but they all implement at least the `League\Url\Interfaces\Component` Interface.

Whenever applicable URL normalization techniques which preserved the component semantics are applied for better interoperability on each component.

## The Component Interface

Each component class implements the `League\Url\Interfaces\Component` with the following public methods:

### Component::withValue($data)

The `$data` argument represents the data to create a new instance of the component:

- a string representation of a component.
- another `Component` object
- an object with the `__toString` method.

### Component::get()

Returns the current data attached to the component as a string or `null` if no data is attached to the component

~~~php

use League\Url\Path;

$user = new User();
$user->get(); // returns 'null'
$new_user = $user->withValue('john');
$new_user->get(); // returns 'john';
~~~

### Component::__toString()

Returns the string representation of the component. While the `Component::get()` method returns `null` when no data is attached to the component class, `Component::__toString()` always return an string.

### Component::getUriComponent()

Returns the string representation of the component with the added URL specific delimiter when applicable.

~~~php
use League\Url\Scheme;

$scheme = new Scheme();
$scheme->get(); // returns 'null'
echo $scheme;  // returns ''
echo $scheme->getUriComponent(); // returns ''
$new_scheme = $scheme->withValue('https');
$new_scheme->get(); // returns 'https';
echo $new_scheme;  // returns 'https';
echo $new_scheme->getUriComponent(); // returns 'https:'
~~~

### Component::sameValueAs(Component $component)

Tells whether two `Component` objects share the same data. Internally this method compares the result of the `Component::getUriComponent()` methods.

~~~php
use League\Url\Port;
use League\Url\Pass;

$port = new Port(8042);
$pass = new Pass(8042);
$port->sameValueAs($pass); //returns false
~~~

<h2 id="simple-components">Single Value Components</h2>

The URL components classes which represent single values only:

* implement the `League\Url\Interface\Component` interface.
* differ in the way they validate and/or output the components.

These classes are:

* `League\Url\Scheme` which deals with the scheme component;
* `League\Url\User` which deals with the user component;
* `League\Url\Pass` which deals with the pass component;
* `League\Url\Port` which deals with the port component;
* `League\Url\Fragment` which deals with the fragment component;

<h2 id="complex-components">Multiple Values Components</h2>

Aside from these simple components, Urls contains 3 more complex components namely:

* `League\Url\Host` which deals with [the host component](/dev-master/host/);
* `League\Url\Path` which deals with [the path component](/dev-master/path/);
* `League\Url\Query` which deals with [the query component](/dev-master/query/);

Each of these classes takes into account the specifity of its related component.
