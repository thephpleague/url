---
layout: default
title: URL complex components
---

<p class="message-notice">This version is still an alpha. The features and documentation may still vary until released</p>

# The Host class

The class

This [segment values component class](/components/overview/#segment-components) manage the URL host component by implementing the following interfaces: 

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\HostInterface`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

This `HostInterface` interface provides methods to deal with <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank"><abbr title="Internationalized Domain Name">IDN</abbr></a> as well as IP like hostname using the following method. This interface extends [`ComponentInterface`](/dev-master/component/) by adding the following methods:

* `toArray()`: return an array representation of the `League\Path` object.
* `keys`: return an array of the keys used in the path.
* `append($data, $whence = null, $whence_index = null)`: append data to the component
* `prepend($data, $whence = null, $whence_index = null)`: prepend data to the component
* `remove($data)`: remove part of the component
* `getSegment($key, $default = null)`: return a segment parameter according to its offset in it does not exists you can provide a default value
* `isIp()` : Tell whether the current host is an IP address
* `isIpv4()` : Tell whether the current host is an IPv4 address
* `isIpv6()` : Tell whether the current host is an IPv6 address
* `toUnicode()` : is an alias of `__toString()` and return the hostname internationalized name
* `toAscii()` : return the Punycode encoded hostname;

~~~php
use League\Url\Components\Host;

$host = new Host;
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