---
layout: default
title: The Host component
---

# The Host component

This [URL multiple values component](/dev-master/component/#complex-components) is manage by implementing the following interfaces:

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\HostInterface`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

## The Host class

### Host::__construct($data = null)

The class constructor takes a single argument `$data` which can be:

- a string representation of a hostname.
- an `array`
- a `Traversable` object
- another `HostInterface` object

~~~php

use League\Url\Host;

$host = new Host('master.example.com');
$alt = new Host($host);
$alt->sameValueAs($host); //returns true
~~~

## The HostInterface

This interface provides methods to deal with <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank"><abbr title="Internationalized Domain Name">IDN</abbr></a> as well as IP like hostname by extending the [ComponentInterface](/dev-master/component/#the-componentinterface) interface with the following methods.

### HostInterface::toArray()

Returns an array representation of the host string

~~~php

use League\Url\Host;

$host = new Host('master.example.com');
$arr = $host->toArray(); returns //  ['master', 'example', 'com'];
~~~

### HostInterface::keys()

Returns the keys of the Host object. If an argument is supplied to the method. Only the keys whose value equals the argument are returned.

~~~php

use League\Url\Host;

$host = new Host('uk.example.co.uk');
$arr = $host->keys(); returns //  [0, 1, 2, 3];
$arr = $host->keys('uk'); returns // ['0, 3];
~~~

### HostInterface::getLabel($offset, $default = null)

Returns the value of a specific offset. If the offset does not exists it will return the value specified by the `$default` argument

~~~php

use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->getLabel(0); //returns 'uk'
$host->getLabel(23); //returns null
$host->getLabel(23, 'now'); //returns 'now'
~~~

### HostInterface::setLabel($offset, $value)

Set a specific key from the object. `$offset` must be an integer between 0 and the total number of label. If `$value` is empty or equals `null`, the specified key will be deleted from the current object.

~~~php

use League\Url\Host;

$host = new Host();
count($host); // returns 0
$host->setLabel(0, 'bar');
$host->getLabel(0); //returns 'bar'
count($host); // returns 1
$host->setLabel(0, null); //returns null
count($host); // returns 0
$host->getLabel(0); //returns null
~~~

### HostInterface::append($data, $whence = null, $whence_index = null)

Append data to the component.

- The `$data` argument which represents the data to be appended can be:
    - a string representation of a host component;
    - an `array`
    - a `Traversable` object
- The `$whence` argument represents the label **after which** the data will be included
- The `$whence_index` represents the the index of the `$whence` argument if present multiple times

~~~php

use League\Url\Host;

$host = new Host();
$host->append('toto');
$host->append('example.com', 'toto');
$host->__toString(); //returns toto.example.com
~~~

### HostInterface::prepend($data, $whence = null, $whence_index = null)

Prepend data to the component.

- The `$data` argument which represents the data to be prepended can be:
    - a string representation of a host component;
    - an `array`
    - a `Traversable` object
- The `$whence` argument represents the label **before which** the data will be included
- The `$whence_index` represents the the index of the `$whence` argument if present multiple times

~~~php

use League\Url\Host;

$host = new Host();
$host->prepend('example.com');
$host->prepend('toto', 'example.com');
$host->__toString(); //returns toto.example.com
~~~

### HostInterface::remove($data)

Remove part of the `Host` component data. The method returns `true` if the `$data` has been successfully removed. The `$data` argument which represents the data to be prepended can be:
    - a string representation of a host component;
    - an `array`
    - a `Traversable` object

if `$data` is present multiple times in the Host object you must repeat your call to `HostInterface::remove` method as long as the method returns `true`.

~~~php

use League\Url\Host;

$host = new Host('toto.example.com');
$host->remove('toto');
$host->__toString(); //returns example.com
~~~

## IDN support

### HostInterface::toUnicode()

The method is an alias of `__toString()` and return the hostname internationalized name.

### HostInterface::toAscii()

This method returns the punycode encoded hostname.

~~~php
use League\Url\Host;

$host = new Host();
$host->set('스타벅스코리아.com'); //you set the IDN
var_export($host->toArray());
//will display
// array(
//    0 => '스타벅스코리아',
//    1 => 'com'
// )
echo $host->toAscii(); // output 'xn--oy2b35ckwhba574atvuzkc.com'
echo $host->toUnicode();  // output '스타벅스코리아.com'

$host->set('xn--mgbh0fb.xn--kgbechtv'); //you set a ascii hostname
echo $host;  // output 'مثال.إختبار'  //the object output the IDN version
~~~

## Hostname as IP

A hostname as IP can be specified using:
- the constructor;
- the `set` method inherited from the [ComponentInterface](/dev-master/component/) interface

Once the hostname is defined as an IP you can no longer use the `setLabel`, `append`, `prepend` methods to modify its value. The only way to modify the hostname is by setting a new value using the `set` method.

### HostInterface::isIp()

Tells whether the current hostname is a IP address

### HostInterface::isIpv4()

Tells whether the current hostname is a IPv4 address

### HostInterface::isIpv6()

Tells whether the current hostname is a IPv6 address

~~~php
use League\Url\Components\Host;

$host = new Host('127.0.0.1');
var_export($host->toArray()); //will display ['127.0.0.1']

echo $host->isIp(); // returns true
echo $host->isIpv4(); // returns true
echo $host->isIpv6(); // returns false

$host->set('FE80:0000:0000:0000:0202:B3FF:FE1E:8329');

echo $host->isIp(); // returns true
echo $host->isIpv4(); // returns false
echo $host->isIpv6(); // returns true

echo $host->getUriComponent(); // returns '[FE80:0000:0000:0000:0202:B3FF:FE1E:8329]'
~~~
