---
layout: default
title: The Query Object
permalink: components/query/
---

# The Query class

This [multiple values component class](/components/overview/#complex-components) manage the URL query component by implementing the `League\Url\Components\QueryInterface`. 

This interface adds the following method:

* `modify($data)`: update the component data;

<p class="message-info">On output, the query string is encoded following the <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a></p>

Example using the `League\Url\Components\Query` object:

~~~php
use League\Url\Components\Query;

$query = new Query('foo=bar');
$query['baz'] = 'troll';
$query['toto'] = 'le heros';
foreach ($query as $offset => $value) {
	echo "$offset => $value".PHP_EOL;
}
//will echo 
// foo => bar
// baz => troll
// toto => le%20heros

$query->modify(array('foo' => 'baz', 'toto' => null));
//by setting toto to null
//you remove the toto argument from the query_string
//you can get the same result by issuing
unset($query['toto']);


$found = $query->keys('troll');
//$found equals array(0 => 'baz')

echo count($query); //will return 2;
~~~