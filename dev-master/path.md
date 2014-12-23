---
layout: default
title: The Path class
---

<p class="message-notice">This version is still an alpha. The features and documentation may still vary until released</p>

# The Path class

This class manage the URL path component by implementing the following interfaces.

- `Countable`
- `IteratorAggregate`
- `League\Url\Interfaces\PathInterface`

<p class="message-warning">in version 4, this class no longer implements the <code>ArrayAccess</code> interface</p>

This `PathInterface` which extends [`ComponentInterface`](/dev-master/component/) adds the following methods:

* `toArray()`: return an array representation of the `League\Path` object.
* `keys`: return an array of the keys used in the path.
* `append($data, $whence = null, $whence_index = null)`: append data to the component
* `prepend($data, $whence = null, $whence_index = null)`: prepend data to the component
* `remove($data)`: remove part of the component
* `getSegment($key, $default = null)`: return a segment parameter according to its offset in it does not exists you can provide a default value
* `relativeTo(PathInterface $path = null)`: returns a string representation of the relative path from the current object relative to the `$path` given;


Example using the `League\Url\Path` object:

~~~php
use League\Url\Path;

$path = new Path;
$path->append('bar');
$path->append('troll');
foreach ($path as $offset => $value) {
	echo "$offset => $value".PHP_EOL;
}
//will echo
// 0 => bar
// 1 => troll

$path->getSegment(1); //will return 'troll'

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
//if you do not insert you data around the first occurence
//the $whence_index start at 0
$path->prepend('bar', 'troll', 1);
echo $path->get(); //will display "bar/leheros/troll/bar/troll"
$path->remove('troll/bar');
echo $path->getUriComponent(); //will display "/bar/leheros/troll"
~~~