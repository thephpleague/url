---
layout: default
title: The Host component
---

# The Host component

Dealing with a host component is not as trivial as it might look, this class tries to help you manipulate and access host content.


## Host creation

A host is a collection of labels delimited by the host delimiter `.`. So apart from using the [component constructor method](/dev-master/component/#component-instantation), you can use a named constructor with a collection of labels as shown below:

~~~php
use League\Url\Host;

$host =  Host::createFromArray(['shop', 'example', 'com']);
echo $host; //display 'shop.example.com'

$alt_host = Host::createFromArray(['shop', 'example', 'com'], true);
echo $host; //display 'shop.example.com.'

$ip_host = Host::createFromArray(['127.0', '0.1']);
echo $ip_host; //display '127.0.0.1'

Host::createFromArray(['127.0', '0.1'], true);
//throws InvalidArgumentException
~~~

The method expect at most 2 arguments.

- The first argument must be a collection of label (an `array` or a `Traversable object)
- The second argument optional tell whether this is an Fully Qualified Domain Name or not

<p class="message-warning">Since an IP is not domain name, you will received a <code>InvalidArgumentException</code> if you try to setup an IP hostname as a FQDN.</p>

## Host Types

### IP address or Domain name

There are two type of host:

- Hosts represented as IP;
- Hosts represented by domain names;

To determine what type of host you are dealing with the `Host` class provides the `isIp` method:

~~~php
use League\Url\Host;

$host = new Host('::1');
$host->isIp();   //return true

$alt_host = new Host('example.com');
$host->isIp(); //return false;
~~~

### IPv4 or IPv6

Knowing that you have a IP address is an incomplete information. Has you may be dealing with an IPv4 or an IPV6 address.

~~~php
use League\Url\Host;

$host = new Host('::1');
$host->isIp();     //return true
$host->isIpv4();   //return false
$host->isIpv6();   //return true

$alt_host = new Host('127.0.0.1');
$alt_host->isIp();     //return true
$alt_host->isIpv4();   //return true
$alt_host->isIpv6();   //return false
~~~

### Simple domain name or FQDN

To determine whether you have a Fully qualified domain name (FQDN), you can use the following method

~~~php
use League\Url\Host;

$host = new Host('example.com');
$host->isIp();       //return false
$host->isAbsolute(); //return false

$alt_host = new Host('example.com.');
$alt_host->isIp();       //return false
$alt_host->isAbsolute(); //return true

$ip_host = new Host('::1');
$ip_host->isIp();       //return true
$ip_host->isAbsolute(); //return false
~~~

## Other representations

### IDN support

The Host class support the <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank"><abbr title="Internationalized Domain Name">IDN</abbr></a> mechanism. The class exposed two methods for this support:

- `Host::toUnicode()` an alias of `__toString()` and returns the hostname internationalized name.
- `Host::toAscii()` returns the punycode encoded hostname.

~~~php
use League\Url\Host;

$host = new Host('스타벅스코리아.com'); //you set the IDN

echo $host->toAscii();   // output 'xn--oy2b35ckwhba574atvuzkc.com'
echo $host->toUnicode(); // output '스타벅스코리아.com'

$host = new Host('xn--mgbh0fb.xn--kgbechtv'); //you set a ascii hostname
echo $host;  // output 'مثال.إختبار'  //the object output the IDN version
~~~

### Host as an array

A host can be divided into its different labels. The class provide an array representation of a the host label usung the `Host::toArray` method.

~~~php
use League\Url\Host;

$host = new Host('secure.example.com');
$arr = $host->toArray(); // returns  ['secure', 'example', 'com'];

$host = new Host('::1');
$arr = $host->toArray(); // returns  ['::1'];
~~~

## Accessing Host Content

### Countable and IteratorAggregate

Apart from its string representation the class provides several methods to works with its labels. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of labels and use the `foreach` construct to iterate overs them.

~~~php
$host = new Host('secure.example.com');
count($host); //return 3
foreach ($host as $offset => $label) {
	//do something meaningfull here
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

## Modifying Host Content

<p class="message-notice">If the modifications does not change the current object it is returned, otherwise, a new modified host object is returned.</p>

### Remove labels

To remove labels from the current object and returns a new `Host` object without the removed labels you can use the `Host::without` method. This methods expected a single argument `$offsets` which is an array containing a list of offsets to remove.

~~~php
use League\Url\Host;

$host    = new Host('toto.example.com');
$newHost = $host->without([1]);
$newHost->__toString(); //returns toto.com
~~~

### Append labels

<p class="message-warning">Trying to append an IP Host with throw an <code>Exception</code></p>

To append labels to the current host you need to use the `Host::append` method. This method accept a single `$data` argument which represents the data to be appended. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Host;

$host    = new Host();
$newHost = $host->append('toto')->append('example.com');
$newHost->__toString(); //returns toto.example.com
~~~

### Prepend labels

<p class="message-warning">Trying to prepend an IP Host with throw an <code>Exception</code></p>

To prepend labels to the current host you need to use the `Host::prepend` method. This method accept a single `$data` argument which represents the data to be prepended. This data can be a string or an object with the `__toString` method.

~~~php
use League\Url\Host;

$host    = new Host();
$newHost = $host->prepend('example.com')->prepend('toto');
$newHost->__toString(); //returns toto.example.com
~~~

### Replace label

Last but not least you can also replace a label with your own data. To do so you must use the `Host::replace` method with the following arguments:

- `$data` which represents the data to be inject. This data can be a string or an object with the `__toString` method.
- `$offset` which represents the label's offset to remove if it exists.

~~~php
use League\Url\Host;

$host    = new Host('foo.example.com');
$newHost = $host->replace('bar.baz', 0);
$newHost->__toString(); //returns bar.baz.example.com
~~~

