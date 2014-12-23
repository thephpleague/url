---
layout: default
title: URL complex components
---

# The Host class

This [segment values component class](/components/overview/#segment-components) manage the URL host component by implementing the `League\Url\Components\HostInterface`.

## IDN support (since version 3.1)

The interface provides methods to deal with <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank"><abbr title="Internationalized Domain Name">IDN</abbr></a>.

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