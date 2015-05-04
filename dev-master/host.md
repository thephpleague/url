---
layout: default
title: The Host component
---

# The Host component

This component is manage throught the `Host` class which implements the following interfaces:

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\Component`
- `League\Url\Interfaces\SegmentComponent`
- `League\Url\Interfaces\Host`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

These interfaces provide methods to deal with <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank"><abbr title="Internationalized Domain Name">IDN</abbr></a> as well as IP like hostname by extending the [Component](/dev-master/component/#the-componentinterface) interface with the following methods:

## The Host class

### Host::__construct($data = null)

The class constructor takes a single argument `$data` which can be:

- a string representation of a hostname.
- another `HostInterface` object
- an object with the `__toString` method.

~~~php
use League\Url\Host;

$host = new Host('master.example.com');
$alt = new Host($host);
$alt->sameValueAs($host); //returns true
~~~

### Host::createFromArray($data, $is_absolute = false)

To ease instantiation you can use this named constructor to generate a new `Host` object from an `array` or a `Traversable` object. The second argument is used to indicate wether the `Host` is a Full Qualified Domain Name which ends with a dot.

~~~php
use League\Url\Host;

echo Host::createFromArray(['bar', 'baz'])->__toString(); //returns 'bar.baz'
echo Host::createFromArray(['shop', 'example.com'], true)->__toString(); //returns 'shop.example.com.'
~~~

### Path::isAbsolute()

At any given time you can verify if the Host you are currently manipulating is a full qualified domain name.

~~~php
use League\Url\Host;

$host = Host::createFromArray(['bar', 'baz']);
$host->isAbsolute(); // returns false;
$host->__toString(); // display bar.baz

$fqdn = Host::createFromArray(['bar', 'baz'], true);
$fqdn->isAbsolute(); // returns true;
$fqdn->__toString(); // display bar.baz.
~~~

### Host::toArray()

Returns the `Host` object as an array of label.

~~~php
use League\Url\Host;

$host = new Host('secure.example.com');
$arr = $host->toArray(); // returns  ['secure.example.com];

$host = new Host('::1');
$arr = $host->toArray(); // returns  ['::1'];
~~~

### Host::getLabel($offset, $default = null)

Returns the value of a specific offset. If the offset does not exists it will return the value specified by the `$default` argument

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->getLabel(0); //returns 'uk'
$host->getLabel(23); //returns null
$host->getLabel(23, 'now'); //returns 'now'
~~~

### Host::offsets($label = null)

Returns the keys of the Host object. If an argument is supplied to the method, only the keys whose label value equals the argument are returned.

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->keys(); // returns  [0, 1, 2, 3];
$host->keys('uk'); // returns [0, 3];
$host->offsets('gweta'); // returns [];
~~~

### Host::hasOffset($offset)

Returns `true` if the submitted `$offset` exists in the current object.

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->hasOffset(2); // returns true
$host->hasOffset(23); // returns false
~~~

### Host::append(SegmentComponent $data)

Append data to the component.

Prepend data to the component. The `$data` argument which represents the data to be appended can be another `SegmentComponent` implementing object. So you can use a Host and/or a Path object.

~~~php
use League\Url\Host;

$host = new Host();
$newHost = $host->append(new Host('toto'))->append(new Host('example.com'));
$newHost->__toString(); //returns toto.example.com
~~~

### Host::prepend(SegmentComponent $data)

Prepend data to the component. The `$data` argument which represents the data to be prepended can be another `SegmentComponent` implementing object. So you can use a Host and/or a Path object.

~~~php
use League\Url\Host;

$host = new Host();
$newHost = $host->prepend(new Host('example.com'))->prepend(new Host('toto'));
$host->__toString(); //returns toto.example.com
~~~

### Host::replace(SegmentComponent $data, $offset)

Replace a Host label whose offset equals `$offset` with the `SegmentComponent` object given as first argument.

~~~php
use League\Url\Host;

$host = new Host('foo.example.com');
$newHost = $host->replace(new Host('bar.baz'), 0);
$host->__toString(); //returns bar.baz.example.com
~~~

### Host::without(array $offsets = [])

Remove labels from the current object and returns a new `Host` object without the removed labels.

The `$offsets` argument is an array containing a list of offsets to remove.

~~~php
use League\Url\Host;

$host = new Host('toto.example.com');
$host->without([1]);
$host->__toString(); //returns toto.com
~~~

## IDN support

### Host::toUnicode()

The method is an alias of `__toString()` and return the hostname internationalized name.

### Host::toAscii()

This method returns the punycode encoded hostname.

~~~php
use League\Url\Host;

$host = new Host('스타벅스코리아.com'); //you set the IDN

echo $host->toAscii();   // output 'xn--oy2b35ckwhba574atvuzkc.com'
echo $host->toUnicode(); // output '스타벅스코리아.com'

$host = new Host('xn--mgbh0fb.xn--kgbechtv'); //you set a ascii hostname
echo $host;  // output 'مثال.إختبار'  //the object output the IDN version
~~~

## Hostname as IP


### Host::isIp()

Tells whether the current hostname is a IP address

### Host::isIpv4()

Tells whether the current hostname is a IPv4 address

### Host::isIpv6()

Tells whether the current hostname is a IPv6 address

~~~php
use League\Url\Components\Host;

$host = new Host('127.0.0.1');
echo $host; //returns '127.0.0.1'

echo $host->isIp();   // returns true
echo $host->isIpv4(); // returns true
echo $host->isIpv6(); // returns false

$new_host = $host->withValue('FE80:0000:0000:0000:0202:B3FF:FE1E:8329');

echo $new_host->isIp();   // returns true
echo $new_host->isIpv4(); // returns false
echo $new_host->isIpv6(); // returns true

echo $new_host->getUriComponent(); // returns '[FE80:0000:0000:0000:0202:B3FF:FE1E:8329]'
~~~
