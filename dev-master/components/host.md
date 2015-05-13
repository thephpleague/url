---
layout: default
title: The Host component
---

# The Host component

The library provides a `League\Url\Host` class to ease complex host manipulation.

## Host creation

### Using the default constructor

A new `League\Url\Host` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

~~~php
use League\Url\Host;

$host = new Host('shop.example.com');
echo $host; //display 'shop.example.com'

$fqdn = new Host('shop.example.com.');
echo $fqdn; //display 'shop.example.com.'

$ipv4 = new Host('127.0.0.1');
echo $ipv4; //display '127.0.0.1'

$ipv6 = new Host('::1');
echo $ipv6; //display '[::1]'

$ipv6_alt = new Host('[::1]');
echo $ipv6_alt; //display '[::1]'
~~~

<p class="message-warning">If the submitted value is not a valid host an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a named constructor

A host is a collection of labels delimited by the host delimiter `.`. So it is possible to create a `Host` object using a collection of labels with the `Host::createFromArray` method.

The method expects at most 2 arguments.

- The first required argument must be a collection of label (an `array` or a `Traversable` object)
- The second optional argument, a boolean, tells whether this is an <abbr title="Fully Qualified Domain Name">FQDN</abbr> or not. By default this optional argument equals `false`.

<p class="message-warning">Since an IP is not a domain name, the class will throw an <code>InvalidArgumentException</code> if you try to create an IP hostname as a FQDN.</p>

~~~php
use League\Url\Host;

$host = Host::createFromArray(['shop', 'example', 'com']);
echo $host; //display 'shop.example.com'

$fqdn = Host::createFromArray(['shop', 'example', 'com'], true);
echo $fqdn; //display 'shop.example.com.'

$ip_host = Host::createFromArray(['127.0', '0.1']);
echo $ip_host; //display '127.0.0.1'

Host::createFromArray(['127.0', '0.1'], true);
//throws InvalidArgumentException
~~~

## Normalization

Whenever you create a new host. You submitted data is normalized using non desctructive operations:

- the host is lowercased;
- the bracket are added if you are instantiating a IPV6 Host;
- the host is encoded in its <abbr title="Internationalized Domain Name">IDN</abbr> version;

~~~php
use League\Url\Host;

$host = Host::createFromArray(['shop', 'ExAmPle', 'com']);
echo $host; //display 'shop.example.com'

$ipv6 = new Host('::1');
echo $ipv6; //display '[::1]'
~~~

## Host types

### IP address or registered name

There are two type of host:

- Hosts represented by an IP;
- Hosts represented by a host names;

To determine what type of host you are dealing with the `Host` class provides the `isIp` method:

~~~php
use League\Url\Host;

$host = new Host('::1');
$host->isIp();   //return true

$alt_host = new Host('example.com');
$host->isIp(); //return false;
~~~

### IPv4 or IPv6

Knowing that you are dealing with an IP is good, knowing that its an IPv4 or an IPv6 is better.

~~~php
use League\Url\Host;

$ipv6 = new Host('::1');
$ipv6->isIp();   //return true
$ipv6->isIpv4(); //return false
$ipv6->isIpv6(); //return true

$ipv4 = new Host('127.0.0.1');
$ipv4->isIp();   //return true
$ipv4->isIpv4(); //return true
$ipv4->isIpv6(); //return false
~~~

### Simple or fully qualified domain name

If you don't have a IP then you are dealing with a host name. A host name is a [domain name](http://tools.ietf.org/html/rfc1034) subset according to [RFC1123](http://tools.ietf.org/html/rfc1123#section-2.1). As such a host name can not, for example, contain an `_`.

A host name is considered absolute or as a Fully Qualified Domain Name (FQDN) if it ends with a `.`.

~~~php
use League\Url\Host;

$host = new Host('example.com');
$host->isIp();       //return false
$host->isAbsolute(); //return false

$fqdn = new Host('example.com.');
$fqdn->isIp();       //return false
$fqdn->isAbsolute(); //return true

$ip = new Host('::1');
$ip->isIp();       //return true
$ip->isAbsolute(); //return false
~~~

<p class="message-warning">The library does not validate your registered name against a valid <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## Host representations

### String representation

Basic host representations is done using the following methods:

~~~php
use League\Url\Host;

$host = new Host('example.com');
$host->__toString();      //return 'example.com'
$host->getUriComponent(); //return 'example.com'

$ipv4 = new Host('127.0.0.1');
$ipv4->__toString();      //return '127.0.0.1'
$ipv4->getUriComponent(); //return '127.0.0.1'

$ipv6 = new Host('::1');
$ipv6->__toString();      //return '[::1]'
$ipv6->getUriComponent(); //return '[::1]'
~~~

### IDN support

The `Host` class supports the <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank"><abbr title="Internationalized Domain Name">IDN</abbr></a> mechanism.

- The `Host::__toString` method always returns the IDN version of the hostname.
- To access the punycode encoded domain name you must used the `Host::toAscii` method.

~~~php
use League\Url\Host;

$host = new Host('스타벅스코리아.com'); //you set the IDN

echo $host->toAscii();   // output 'xn--oy2b35ckwhba574atvuzkc.com'
echo $host; // output '스타벅스코리아.com'

$host = new Host('xn--mgbh0fb.xn--kgbechtv'); //you set a ascii hostname
echo $host;  // output 'مثال.إختبار'  //the object output the IDN version
echo $host->toAscii();  // output 'xn--mgbh0fb.xn--kgbechtv'  //the object output the Ascii version
~~~

### Array representation

A host can be divided into its different labels. The class provides an array representation of a the host labels using the `Host::toArray` method.

<p class="message-warning">Once in array representation you can not distinguish a simple from a fully qualified domain name.</p>

~~~php
use League\Url\Host;

$host = new Host('secure.example.com');
$arr = $host->toArray(); // returns  ['secure', 'example', 'com'];

$fqdn = new Host('secure.example.com.');
$arr = $fqdn->toArray(); // returns  ['secure', 'example', 'com'];

$host = new Host('::1');
$arr = $host->toArray(); // returns  ['::1'];
~~~

## Accessing host contents

### Countable and IteratorAggregate

The class provides several methods to works with its labels. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of labels and use the `foreach` construct to iterate overs them.

~~~php
$host = new Host('secure.example.com');
count($host); //return 3
foreach ($host as $offset => $label) {
    //do something meaningful here
}
~~~

### Label offsets

If you are interested in getting all the label offsets you can do so using the `Host::offsets` method like show below:

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->keys(); // returns  [0, 1, 2, 3];
$host->keys('uk'); // returns [0, 3];
$host->offsets('gweta'); // returns [];
~~~

The methods returns all the label offsets, but if you supply an argument, only the offsets whose label value equals the argument are returned.

If you want to be sure that an offset exists before using it you can do so using the `Host::hasOffset` method which returns `true` if the submitted `$offset` exists in the current object.

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->hasOffset(2); // returns true
$host->hasOffset(23); // returns false
~~~

### Label content

If you are only interested in a given label you can access it directly using the `Host::getLabel` method as show below:

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->getLabel(0); //returns 'uk'
$host->getLabel(23); //returns null
$host->getLabel(23, 'now'); //returns 'now'
~~~

The method returns the value of a specific offset. If the offset does not exists it will return the value specified by the second `$default` argument.

## Modifying host contents

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

### Append labels

<p class="message-warning">Trying to append to or with an IP host will throw an <code>InvalidArgumentException</code></p>

To append labels to the current host you need to use the `Host::append` method. This method accept a single `$data` argument which represents the data to be appended. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Host;

$host    = new Host();
$newHost = $host->append('toto')->append('example.com');
$newHost->__toString(); //returns toto.example.com
~~~

### Prepend labels

<p class="message-warning">Trying to prepend to or with an IP Host will throw an <code>InvalidArgumentException</code></p>

To prepend labels to the current host you need to use the `Host::prepend` method. This method accept a single `$data` argument which represents the data to be prepended. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Host;

$host    = new Host();
$newHost = $host->prepend('example.com')->prepend('toto');
$newHost->__toString(); //returns toto.example.com
~~~

### Replace label

To replace a label with your own data, you must use the `Host::replace` method with the following arguments:

- `$data` which represents the data to be inject. This data can be a string or an object with the `__toString` method.
- `$offset` which represents the label's offset to remove if it exists.

~~~php
use League\Url\Host;

$host    = new Host('foo.example.com');
$newHost = $host->replace('bar.baz', 0);
$newHost->__toString(); //returns bar.baz.example.com
~~~

### Remove labels

To remove labels from the current object and returns a new `Host` object without the removed labels you can use the `Host::without` method. This methods expected a single argument `$offsets` which is an array containing a list of offsets to remove.

~~~php
use League\Url\Host;

$host    = new Host('toto.example.com');
$newHost = $host->without([1]);
$newHost->__toString(); //returns toto.com
~~~
