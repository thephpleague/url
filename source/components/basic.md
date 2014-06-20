---
layout: layout
title: URL Components
---

# URL components

Each component class implements the `League\Url\Components\ComponentInterface` with the following public methods:

* `set($data)`: set the component data
* `get()`: returns `null` if the class data is empty or its string representation
* `__toString()`: return a typecast string representation of the component.
* `getUriComponent()`: return an altered string representation to ease URL representation.

The `$data` argument can be:

* `null`;
* a valid component string for the specified URL component;
* an object implementing the `__toString` method;

~~~.language-php
use League\Url\Components\Scheme;

$scheme = new Scheme;
$scheme->get(); //will return null since no scheme was set
$scheme->set('https');
echo $scheme->__toString(); //will echo 'https'
echo $scheme->getUriComponent(); //will echo 'https://'

$scheme_new = new Scheme;
echo $scheme_new->get(); // will echo 'https';
~~~

<p class="message-info"><strong>Tips:</strong> The <code>exchange</code> method also copy the internal property of the object while the <code>set</code> method only sets the object data.</p>

The URL components that **only** implement this interface are:

* `scheme` with the `League\Url\Components\Scheme`;
* `user` with the `League\Url\Components\User`;
* `pass` with the `League\Url\Components\Pass`;
* `port` with the `League\Url\Components\Port`;
* `fragment` with the `League\Url\Components\Fragment`;
