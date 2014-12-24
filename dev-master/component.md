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

The `League\Url` library provides an access to each URL components via a set of interfaces and classes. These classes can all be use independently but they all implement at least the `League\Url\Interfaces\ComponentInterface`.

##The ComponentInterface

Each component class implements the `League\Url\Interfaces\ComponentInterface` with the following public methods:

### ComponentInterface::set($data)

Sets the component data.

The `$data` argument can be:

* `null`;
* a string;
* an `array` or a `Traversable` object, for complex components;
* an object implementing the `__toString` method;
* an object implementing the `ComponentInterface` interface;

### ComponentInterface::get()

Returns the current data attached to the component as a string or null if no data is attached to the component

~~~php

use League\Url\Path;

$path = new Path();
$path->get(); // returns 'null'
$path->set('/path/to/heaven');
$path->get(); // returns 'path/to/heaven';
$path->set(['path', 'to', 'my', 'crib']);
$path->get(); // returns 'path/to/my/crib';
~~~

### ComponentInterface::__toString()

Returns the string representation of the component. While the `ComponentInterface::get()` method returns null when no data is attached to the component class, `ComponentInterface::__toString()` return an empty string.

### ComponentInterface::getUriComponent()

Returns an altered string representation to ease URL representation.

~~~php

use League\Url\Scheme;

$scheme = new Scheme();
$scheme->get(); // returns 'null'
echo $scheme;  // returns ''
echo $scheme->getUriComponent(); returns '//'
$scheme->set('https');
$scheme->get(); // returns 'https';
echo $scheme;  // returns 'https';
echo $scheme->getUriComponent(); returns 'https://'
~~~

### ComponentInterface::sameValueAs(ComponentInterface $component)

Tells whether two `ComponentInterface` objects share the same string representation.

~~~php

use League\Url\Port;
use League\Url\Pass;

$port = new Port(8042);
$pass = new Pass(8042);
$port->sameValueAs($pass); // returns true because $pass->__toString() equals $port->__toString();
~~~

<h2 id="simple-components">Single Value Components</h2>

The URL components classes which represent single values only:

* implement the `League\Url\Interface\ComponentInterface` interface.
* differ in the way they validate and/or output the components.

These classes are:

* `League\Url\Scheme` which deals with the scheme component;
* `League\Url\User` which deals with the user component;
* `League\Url\Pass` which deals with the pass component;
* `League\Url\Port` which deals with the port component;
* `League\Url\Fragment` which deals with the fragment component;

<h2 id="complex-components">Multiple Values Components</h2>

Aside the single value components,  Urls contains 3 more complex components namely:

* `League\Url\Query` which deals with [the query component](/dev-master/query/);
* `League\Url\Path` which deals with [the path component](/dev-master/path/);
* `League\Url\Host` which deals with [the host component](/dev-master/host/);

Each of these classes takes into account the specifity of its related component.
