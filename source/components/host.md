---
layout: layout
title: URL complex components
---

# The Host class

This [multiple values component class](/components/overview/#complex-components) manage the URL host component by implementing the `League\Url\Components\SegmentInterface` just like the [League\Url\Components\Path](/components/path/) class. 

<p class="message-warning"><code>League\Url\Components\Host</code> only validates the host syntax but not its existence using a <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

## IDN support (since version 3.1)

The `League\Url\Components\Host` also implements the `League\Url\Components\HostInterface` which provides methods to deal with <a href="http://en.wikipedia.org/wiki/Internationalized_domain_name" target="_blank">IDN</a>.

* `toUnicode()` : is an alias of `__toString()` and return the hostname internationalized name
* `toAscii()` : return the Punycode encoded hostname; 

~~~.language-php
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