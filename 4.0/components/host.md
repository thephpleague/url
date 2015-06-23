---
layout: default
title: The Host component
---

# The Host component

The library provides a `League\Url\Host` class to ease complex host manipulation.

## Host creation

### Using the default constructor

A new `League\Url\Host` object can be instantiated using the default constructor.

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

### Using a League\Url\Url object

You can also get a `Host` object from an `League\Url\Url` class:

~~~php
use League\Url\Url;

$url  = Url::createFromUrl('http://url.thephpleague.com/');
$host = $url->host; // $host is a League\Url\Host object;
~~~

### Using a named constructor

A host is a collection of labels delimited by the host delimiter `.`. So it is possible to create a `Host` object using a collection of labels with the `Host::createFromArray` method.

The method expects at most 2 arguments:

- The first required argument must be a collection of label (an `array` or a `Traversable` object)
- The second optional argument, a `Host` constant, tells whether this is an <abbr title="Fully Qualified Domain Name">FQDN</abbr> or not:
    - `Host::IS_ABSOLUTE` creates an object with a FQDN;
    - `Host::IS_RELATIVE` creates an object with a relative domain name;

By default this optional argument equals to `Host::IS_RELATIVE`.

<p class="message-warning">Since an IP is not a hostname, the class will throw an <code>InvalidArgumentException</code> if you try to create an FQDN hostname with a valid IP address.</p>

~~~php
use League\Url\Host;

$host = Host::createFromArray(['shop', 'example', 'com']);
echo $host; //display 'shop.example.com'

$fqdn = Host::createFromArray(['shop', 'example', 'com'], Host::IS_ABSOLUTE);
echo $fqdn; //display 'shop.example.com.'

$ip_host = Host::createFromArray(['127.0', '0.1']);
echo $ip_host; //display '127.0.0.1'

Host::createFromArray(['127.0', '0.1'], Host::IS_ABSOLUTE);
//throws InvalidArgumentException
~~~

## Normalization

Whenever you create a new host your submitted data is normalized using non desctructive operations:

- the host is lowercased;
- the bracket are added if you are instantiating a IPV6 Host;
- the host is encoded using the punycode algorithm to take into account <abbr title="Internationalized Domain Name">IDN</abbr>;

~~~php
use League\Url\Host;

$host = Host::createFromArray(['shop', 'ExAmPle', 'com']);
echo $host; //display 'shop.example.com'

$ipv6 = new Host('::1');
echo $ipv6; //display '[::1]'
~~~

## Host types

### IP address or hostname

There are two type of host:

- Hosts represented by an IP;
- Hosts represented by a hostname;

To determine what type of host you are dealing with the `Host` class provides the `isIp` method:

~~~php
use League\Url\Host;
use League\Url\Url;

$host = new Host('::1');
$host->isIp();   //return true

$alt_host = new Host('example.com');
$host->isIp(); //return false;

Url::createFromServer($_SERVER)->host->isIp(); //return a boolean
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

### Relative or fully qualified domain name

If you don't have a IP then you are dealing with a host name. A host name is a [domain name](http://tools.ietf.org/html/rfc1034) subset according to [RFC1123](http://tools.ietf.org/html/rfc1123#section-2.1). As such a host name can not, for example, contain an `_`.

A host name is considered absolute or as being a fully qualified domain name (FQDN) if it ends with a `.`, otherwise it is known as being a relative or a partially qualified domain name (PQDN).

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

- The `Host::__toString` method always returns the punycode encoded hostname.
- To access the IDN domain name you must used the `Host::toUnicode` method.

~~~php
use League\Url\Host;
use League\Url\Url;

$host = new Host('스타벅스코리아.com'); //you set the IDN

echo $host->__toString();   // output 'xn--oy2b35ckwhba574atvuzkc.com'
echo $host->toUnicode; // output '스타벅스코리아.com'

$host = new Host('xn--mgbh0fb.xn--kgbechtv'); //you set a ascii hostname
echo $host;  // output 'xn--mgbh0fb.xn--kgbechtv'  //the object output the Ascii version
echo $host->toUnicode(); // output 'مثال.إختبار'   //the object output the IDN version

echo Url::createFromServer($_SERVER)->host->toUnicode(); //output the IDN version
~~~

### Array representation

A host can be exploded into its different labels. The class provides an array representation of a the host labels using the `Host::toArray` method.

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

The class provides several methods to works with its labels. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of labels and use the `foreach` construct to iterate over them.

~~~php
$host = new Host('secure.example.com');
count($host); //return 3
foreach ($host as $offset => $label) {
    //do something meaningful here
}
~~~

### Label offsets

If you are interested in getting all the label offsets you can do so using the `Host::offsets` method like shown below:

~~~php
use League\Url\Host;

$host = new Host('uk.example.co.uk');
$host->offsets(); // returns  [0, 1, 2, 3];
$host->offsets('uk'); // returns [0, 3];
$host->offsets('gweta'); // returns [];
~~~

The methods returns all the label offsets, but if you supply an argument, only the offsets whose label value equals the argument are returned.

To know If an offset exists before using it you can use the `Host::hasOffset` method which returns `true` or `false` depending on the presence or absence of the submitted `$offset` in the current object.

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

The method returns the value of a specific offset. If the offset does not exists it will return the value specified by the second `$default` argument or `null`.

## Modifying the host

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

### Append labels

<p class="message-warning">Trying to append to or with an IP host will throw an <code>InvalidArgumentException</code></p>

To append labels to the current host you need to use the `Host::append` method. This method accept a single `$data` argument which represents the data to be appended. This data can be a string, an object which implements the `__toString` method or another `Host` object:

~~~php
use League\Url\Host;

$host    = new Host();
$newHost = $host->append('toto')->append(new Host('example.com'));
$newHost->__toString(); //returns toto.example.com
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::appendHost</code></p>

### Prepend labels

<p class="message-warning">Trying to prepend to or with an IP Host will throw an <code>InvalidArgumentException</code></p>

To prepend labels to the current host you need to use the `Host::prepend` method. This method accept a single `$data` argument which represents the data to be prepended. This data can be a string, an object which implements the `__toString` method or another `Host` object:

~~~php
use League\Url\Host;

$host    = new Host();
$newHost = $host->prepend('example.com')->prepend(new Host('toto'));
$newHost->__toString(); //returns toto.example.com
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::prependHost</code></p>

### Replace label

To replace a label with your own data, you must use the `Host::replace` method with the following arguments:

- `$offset` which represents the label's offset to remove if it exists.
- `$data` which represents the data to be inject. This data can be a string, an object which implements the `__toString` method or another `Host` object.

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::replaceLabel</code></p>

~~~php
use League\Url\Host;

$host    = new Host('foo.example.com');
$newHost = $host->replace(0, 'bar.baz');
$newHost->__toString(); //returns bar.baz.example.com
~~~

### Remove labels

To remove labels from the current object you can use the `Host::without` method. This methods expected a single argument and will returns a new `Host` object without the selected labels.

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::withoutLabels</code></p>

The argument can be an array containing a list of offsets to remove.

~~~php
use League\Url\Host;

$host    = new Host('toto.example.com');
$newHost = $host->without([1]);
$newHost->__toString(); //returns toto.com
~~~

Or a callable that will select the list of offsets to remove.

~~~php
use League\Url\Host;

$host    = new Host('toto.example.com');
$newHost = $host->without(function ($value) {
	return $value == 0;
});
echo $newHost; //displays 'example.com';
~~~

### Remove zone identifier

According to [RFC6874](http://tools.ietf.org/html/rfc6874#section-4).

> You **must** remove any ZoneID attached to an outgoing URI, as it has only local significance at the sending host.

To fullfill this requirement, the `Host::withoutZoneIdentifier` method is provided. The method takes not parameter and return a new host instance without its zone identifier, it the host was an IPv6 one with a zone identifier. Otherwise the current instance is returned unchanged.

~~~php
use League\Url\Host;

$host    = new Host('[fe80::1%25eth0-1]');
$newHost = $host->withoutZoneIdentifier();
echo $newHost; //displays '[fe80::1]';
~~~

### Filter labels

You can filter the `Host` object using the `Host::filter` method.

The first parameter must be a `callable`

~~~php
use League\Url\Host;

$host    = new Host('www.11.be');
$newHost = $host->filter(function ($value) {
	return ! is_numeric($value);
});
echo $newHost; //displays 'www.be'
~~~

By specifying the second argument flag you can change how filtering is done:

- use `Host::FILTER_USE_VALUE` to filter according to the label value;
- use `Host::FILTER_USE_KEY` to filter according to the label offset;

By default, if no flag is specified the method will use the `Host::FILTER_USE_VALUE` flag.

~~~php
use League\Url\Host;

$host    = new Host('www.11.be');
$newHost = $host->filter(function ($value) {
	return $value != 1;
}, Host::FILTER_USE_KEY);
echo $newHost; //displays 'www.be'
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::filterHost</code></p>