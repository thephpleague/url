---
layout: default
title: The Scheme Registration System
---

# Scheme registration system

Ouf of the box the library supports the following schemes:

- ftp,
- file,
- http, https
- ssh,
- ws, wss

But sometimes you may want to extend, restrict or change the supported schemes used by the library. To do so the library uses a scheme registration system to help you manage the allowed schemes. The system is controlled through the use of the `League\Uri\Services\SchemeRegistry` class. Once instantiated, this immutable value object can help you:

extend the registry scheme list.

~~~php
use League\Uri\Url;
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->merge(['telnet' => 23]);
$components = parse_url('telnet://foo.example.com');
$url = Url::createFromComponents($components, $newRegistry);
~~~

restrict the registry scheme list.

~~~php
use League\Uri\Url;
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->filter(function ($port) {
	return $port == 80;
});
$url = Url::createFromString('https://foo.example.com', $newRegistry);
//will throw an InvalidArgumentException as only
// the 'http' and the 'ws' scheme are now supported
~~~

or create a totally new scheme registry

~~~php
use League\Uri\Url;
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry(['telnet' => 23]);
$components = parse_url('https://foo.example.com');
$url = Url::createFromComponents($components, $registry);
// will throw an InvalidArgumentException as only
// the 'telnet' scheme is now supported
~~~

Once you have instantiated a registry object you can specify it as an optional argument to the `Url\Scheme` object constructor or one of the `League\Uri\Url` named constructor.

~~~php
use League\Uri\Scheme;
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->filter(function ($port) {
	return $port == 80;
});
$scheme = new Scheme('http', $newRegistry);
~~~

<p class="message-notice">If no <code>SchemeRegistry</code> object is supplied, a default registry object is instantiated with the default supported schemes and their respective standard port and attached to the scheme object.</p>

## Creating the registry

We first need to instantiate a new `SchemeRegistry` object using its constructor. This method accept only one argument which is a array of scheme/standard port pair. Each scheme and pair are syntaxically validated before addition. The schemes are normalized to their lowercase string representation.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry([
	'telnet' => 23,
	'HtTp' => 80,
	'file' => null
]);
//you have registered the 3 schemes
~~~

- The **telnet** scheme standard port is **23**
- The **http** scheme standard port is **80**
- The **file** scheme has no standard port and must be given the `null` value for the associated port

By defaut if no array is provided, the registry is instantiated using the default supported schemes.

## Exporting the registry.

If you are interested in getting the full registry data you can use the `SchemeRegistry::toArray` method. The method will return an array of the currently registered scheme/standard port pairs.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry([
	'telnet' => 23,
	'HtTp' => 80,
	'file' => null
]);
$registry->toArray();
//return [
//	'file' => null,
//	'http' => 80,
//	'telnet' => 23,
//];
~~~

<p class="message-info"><strong>Of note:</strong> the schemes are normalized to their lowercase string representation and the array is sorted according to the scheme names.</p>

## Getting informations from the registry

### Countable and IteratorAggregate

The class provides several methods to works with its schemes. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of schemes and use the `foreach` construct to iterate overs them.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
count($registry); //return an integer
foreach ($registry as $scheme => $port) {
    //do something meaningful here
    //$port is a Url\Port object
}
~~~

### Detecting the scheme

If you only want to know if a particular scheme is registered then you can simply use the `SchemeRegistry::hasKey` method.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->hasKey("telnet"); //return false
$registry->hasKey("ftp");    //return true
~~~

The `telnet` scheme is not registered by default while the `ftp` scheme is registered by defaut.

### Listing available schemes

To list all the registered schemes use the `SchemeRegistry::keys` method. This method will always return an array containing the found scheme as string.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry([
	'telnet' => 23,
	'HtTp' => 80,
	'file' => null
]);
$registry->keys(); //return ['file', 'http', 'telnet'];
~~~

the `SchemeRegistry::keys` method can also list the schemes that share the same standard port.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->keys(80); //return ["http", "ws"]
$registry->keys(352); //return []
~~~

If no scheme is found the method will return an empty array.

### Getting the standard port

To get the standard port for a given scheme you can use the `SchemeRegistry::getPort` method. This method will return a `Url\Port` object representing the found standard port or an `InvalidArgumentException` if the scheme is unknown or invalid.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$registry->getPort('http'); //return new Port(80)
$registry->getPort("telnet"); //throws an InvalidArgumentException
~~~

<p class="message-notice">To avoid the exception it is recommended to first issue a <code>SchemeRegistry::hasKey</code> call prior to calling the <code>SchemeRegistry::getPort</code> method.</p>

## Modifying the registry.

<p class="message-notice">If the modifications do not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> exception is thrown.</p>

### Add or update registered schemes

If you want to add or update the registry you need to use the `SchemeRegistry::merge` method. This method expects a single argument. This argument can be:

An `array` or a `Traversable` object similar the array used to instantiate the class

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->merge(['telnet' => 23, 'gopher' => '70']);
count($registry);    //return 7
count($newRegistry); //return 9
$newRegistry->hasKey('telnet') //return true
~~~

Another `SchemeRegistry` object

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$altregistry = new SchemeRegistry([
	'telnet' => 23,
	'HtTp' => 80,
	'file' => null
]);

$newRegistry = $registry->merge($altregistry);
count($registry);                 //return 7
count($altregistry);              //return 3
count($newRegistry);              //return 8
$newRegistry->hasKey('telnet') //return true
~~~

### Remove schemes

To remove scheme from the registry and returns a new `SchemeRegistry` object without them you must use the `SchemeRegistry::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->without(['https', 'WsS']);
count($registry);    //return 7
count($newRegistry); //return 5
$newRegistry->hasKey('wss'); //return false
~~~

Or a callable that will select the list of parameter names to remove.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->without(function ($port) {
	return $port !== 80;
});
count($registry);    //return 7
count($newRegistry); //return 2
$newRegistry->keys(80); //return ['http', 'ws'];
~~~

### Filter the registry

Another way to reduce the number of scheme from the registry is to filter them.

You can filter the registry according to its scheme or its port using the `SchemeRegistry::filter` method.

The first parameter must be a `callable`

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->filter(function ($port) {
	return $port == 80;
});
count($registry);    //return 7
count($newRegistry); //return 2
$newRegistry->keys(80); //return ['http', 'ws'];
~~~

By specifying the second argument flag you can change how filtering is done:

- use `SchemeRegistry::FILTER_USE_VALUE` to filter according to the scheme;
- use `SchemeRegistry::FILTER_USE_OFFSET` to filter according to the port;

By default, if no flag is specified the method will filter by value.

~~~php
use League\Uri\Services\SchemeRegistry;

$registry = new SchemeRegistry();
$newRegistry = $registry->filter(function ($value) {
	return strpos($value, 'http') === 0;
}, SchemeRegistry::FILTER_USE_OFFSET);
count($registry);    //return 15
count($newRegistry); //return 2
$newRegistry->keys(80); //return ['http', 'https'];
~~~
