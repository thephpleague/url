---
layout: default
title: The Scheme Registration System
---

# Scheme registration system

Ouf of the box the library supports the following schemes:

- ftp, ftps
- file,
- gopher,
- http, https
- ldap, ldaps
- nntp, snews
- ssh,
- ws, wss
- telnet, wais

But sometimes you may want to extend, restrict or change the supported schemes used by the library. To do so the library uses a scheme registration system to help you manage the allowed schemes. The system is controlled through the use of the `League\Url\Services\SchemeRegistry` class. Once instantiated, this immutable value object can help you:

extend the registry scheme list.

~~~php
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = (new SchemeRegistry())->merge(['ssh' => 22]);
$components = parse_url('ssh://foo.example.com');
$url = Url::createFromComponents($components, $registry);
~~~

restrict the registry scheme list.

~~~php
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = (new SchemeRegistry())>filter(function ($port) {
	return $port == 80;
});
$url = Url::createFromUrl('https://foo.example.com', $registry);
//will throw an InvalidArgumentException as only
// the 'http' and the 'ws' scheme are now supported
~~~

or create a totally new scheme registry

~~~php
use League\Url\Url;
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry(['yolo' => 2020]);
$components = parse_url('https://foo.example.com');
$url = Url::createFromComponents($components, $registry);
// will throw an InvalidArgumentException as only
// the 'yolo' scheme is now supported
~~~

Once you have instantiated a registry object you can specify it as an optional argument to the `Url\Scheme` object constructor or one of the `League\Url\Url` named constructor.

~~~php
use League\Url\Scheme;
use League\Url\Services\SchemeRegistry;

$registry = (new SchemeRegistry())>filter(function ($port) {
	return $port == 80;
});
$scheme = new Scheme('http', $registry);
~~~

If no scheme registry object is supplied, a default registry object is instantiated with the default schemes and their standard port attached to the class.

## Creating the registry

We first need to instantiate a new `SchemeRegistry` object using its constructor. This method accept only one argument which is a array of scheme/standard port pair. Each scheme and pair are syntaxically validated before addition. The schemes are normalized to their lowercase string representation.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry([
	'yolo' => 8080,
	'HtTp' => 80,
	'file' => null
]);
//you have registered the 3 schemes
~~~

- The **yolo** scheme standard port is **8080**
- The **http** scheme standard port is **80**
- The **file** scheme has no standard port and must be given the `null` value for the associated port

By defaut if no array is provided, the registry is instantiated using the default supported schemes.

## Exporting the registry.

If you are interested in getting the full registry data you can use the `SchemeRegistry::toArray` method. The method will return an array of the currently registered scheme/standard port pairs.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry([
	'yolo' => 8080,
	'HtTp' => 80,
	'file' => null
]);
$registry->toArray();
//returns [
//	'file' => null,
//	'http' => 80,
//	'yolo' => 8080,
//];
~~~

<p class="message-info"><strong>Of note:</strong> the array is sorted and the scheme normalize to their lowercase string representation.</p>

## Getting informations from the registry

### Countable and IteratorAggregate

The class provides several methods to works with its schemes. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of schemes and use the `foreach` construct to iterate overs them.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
count($registry); //return 4
foreach ($registry as $scheme => $port) {
    //do something meaningful here
}
~~~

### Detecting the scheme

If you only want to know if a particular scheme is registered then you can simply use the `SchemeRegistry::hasOffset` method.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->hasOffset("yolo"); //returns false
$registry->hasOffset("wss"); //return true
~~~

The `yolo` scheme is not registered by default while the `wss` secure websocket scheme is registered by defaut.

### Listing available schemes

To list all the registered schemes use the `SchemeRegistry::offsets` method. This method will always return an array containing the found scheme as string.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry([
	'yolo' => 8080,
	'HtTp' => 80,
	'file' => null
]);
$registry->offsets(); //returns ['file', 'http', 'yolo'];
~~~

the `SchemeRegistry::offsets` method can also list the schemes that share the same standard port.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->offsets(80); //returns ["http", "ws"]
$registry->offsets(352); //returns []
~~~

If no scheme is found the method will return an empty array.

### Getting the standard port

To get the standard port for a given scheme you can use the `SchemeRegistry::getPort` method. This method will return a `Url\Port` object representing the found standard port or an `InvalidArgumentException` if the scheme is unknown or invalid.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->getPort('http'); //returns [new Port(80)]
$registry->getPort("yolo"); //throws an InvalidArgumentException
~~~

<p class="message-notice">To avoid the exception it is recommended to first issue a <code>SchemeRegistry::hasOffset</code> call prior to calling the <code>SchemeRegistry::getPort</code> method.</p>

## Modifying the registry.

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

### Add or update registered schemes

If you want to add or update the registry you need to use the `SchemeRegistry::merge` method. This method expects a single argument. This argument can be:

An `array` or a `Traversable` object similar the array used to instantiate the class

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->merge(['yolo' => 8080, 'r' => '68']);
count($registry); //returns 15
count($newRegistry); //returns 17
$newRegistry->hasOffset('yolo') //returns true
~~~

Another `SchemeRegistry` object

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$altregistry = new SchemeRegistry([
	'yolo' => 8080,
	'HtTp' => 80,
	'file' => null
]);

$newRegistry = $registry->merge($altregistry);
count($registry);    //returns 15
count($altregistry); //returns 3
count($newRegistry); //returns 16
$newRegistry->hasOffset('yolo') //returns true
~~~

### Remove schemes

To remove scheme from the registry and returns a new `SchemeRegistry` object without them you must use the `SchemeRegistry::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->without(['https', 'WsS']);
count($registry);    //returns 15
count($newRegistry); //returns 13
$newRegistry->hasOffset('wss'); //returns false
~~~

Or a callable that will select the list of parameter names to remove.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->without(function ($port) {
	return $port !== 80;
});
count($registry);    //returns 15
count($newRegistry); //returns 2
$newRegistry->offsets(80); //returns ['http', 'ws'];
~~~

### Filter the registry

Another way to reduce the number of scheme from the registry is to filter them.

You can filter the registry according to its scheme or its port using the `SchemeRegistry::filter` method.

The first parameter must be a `callable`

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->filter(function ($port) {
	return $port == 80;
});
count($registry);    //returns 15
count($newRegistry); //returns 2
$newRegistry->offsets(80); //returns ['http', 'ws'];
~~~

By specifying the second argument flag you can change how filtering is done:

- use `SchemeRegistry::FILTER_USE_VALUE` to filter according to the scheme;
- use `SchemeRegistry::FILTER_USE_KEY` to filter according to the port;

By default, if no flag is specified the method will filter by value.

~~~php
use League\Url\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->filter(function ($value) {
	return strpos($value, 'http') === 0;
}, SchemeRegistry::FILTER_USE_KEY);
count($registry);    //returns 15
count($newRegistry); //returns 2
$newRegistry->offsets(80); //returns ['http', 'https'];
~~~
