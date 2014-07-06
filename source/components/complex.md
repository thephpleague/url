---
layout: layout
title: URL complex components
---

# Complex URL components

Classes that deal with complex components (ie: `host`, `path`, `query`) implement the following interfaces:

* `Countable`
* `IteratorAggregate`
* `ArrayAccess`
* `League\Url\Components\ComponentArrayInterface`

The `League\Url\Components\ComponentArrayInterface` extends the [League\Url\Components\ComponentInterface](/components/basic) interface by adding the following methods:

* `toArray()`: will return an array representation of the component;
* `keys()`: will return all the keys or a subset of the keys of an array if a value is given.

<p class="message-info"><strong>Of note:</strong> The <code>$data</code> argument for the <code>set</code> method can also be an <code>array</code> or a <code>Traversable</code> object.</p>

## The Query class

This class manage the URL query component by implementing the `League\Url\Components\QueryInterface`.
This interface extends the `League\Url\Components\ComponentArrayInterface` by adding the following methods:

* `modify($data)`: update the component data;

<p class="message-info">On output, the query string is encoded following the <a href="http://www.faqs.org/rfcs/rfc3968" target="_blank">RFC 3986</a></p>

Example using the `League\Url\Components\Query` object:

~~~.language-php
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

## The Path and Host classes

These classes manage the URL path and host components. They only differ in the way they validate and format before outputting their data. Both classes implement the `League\Url\Components\SegmentInterface` which extends the `League\Url\Components\ComponentArrayInterface` by adding the following methods:

* `append($data, $whence = null, $whence_index = null)`: append data into the component;
* `prepend($data, $whence = null, $whence_index = null)`: prepend data into the component;
* `remove($data)`: remove data from the component;

The arguments:

* The `$data` argument can be `null`, a valid component string, an object implementing the `__toString` method, an array or a `Traversable` object;
* The `$whence` argument specify the string segment where to include the data;
* The `$whence_index` argument specify the `$whence` index if it is present more than once. The value starts at `0`;
* When using the `remove` method, if the pattern is present multiple times only the first match found is removed 

<p class="message-warning"><code>League\Url\Components\Host</code> only validates the host syntax but not its existence using a <a href="https://publicsuffix.org/" target="_blank">public suffix list</a>.</p>

<p class="message-info"><strong>Tips:</strong> You can easily get the <code>$whence_index</code> by using the object <code>keys($whence)</code> method result.</p>

Example using the `League\Url\Components\Path` object:

~~~.language-php
use League\Url\Components\Path;

$path = new Path;
$path[] = 'bar';
$path[] = 'troll';
foreach ($path as $offset => $value) {
	echo "$offset => $value".PHP_EOL;
}
//will echo 
// 0 => bar
// 1 => troll

$path->append('leheros/troll', 'bar');

$found = $path->keys('troll');
//$found equals array(0 => '2');

echo count($path); //will return 4;
echo $path; //will display bar/leheros/troll/troll
var_export($path->toArray());
//will display
// array(
//    0 => 'bar',
//    1 => 'toto',
//    2 => 'troll',
//    3 => 'troll'
// )

$nb_occurences = count($path->keys('troll'));
//if $nb_occurences is higher than 1, 
//you must specify the $whence index 
//if you do not insert your data around the first occurence
//the $whence_index start at 0
$path->prepend('bar', 'troll', 1);
echo $path->get(); //will display "bar/leheros/troll/bar/troll"
$path->remove('troll/bar');
echo $path->getUriComponent(); //will display "/bar/leheros/troll"
~~~
