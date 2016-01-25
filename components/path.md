---
layout: default
title: The Path class
---

# The Path class

This class is a [segment values component class](/components/overview/#segment-components) and manage the URL path component. It also implements the `League\Url\Components\PathInterface`.

The `League\Url\Components\PathInterface` interface adds the following method:

### PathInterface::getRelativePath(PathInterface $path)

<p class="message-notice">added in <code>version 3.2</code></p>

Returns a string representation of the relative path from the current object relative to the `$path` given.

Example using the `League\Url\Components\Path` object:

~~~php
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
//if you do not insert you data around the first occurence
//the $whence_index start at 0
$path->prepend('bar', 'troll', 1);
echo $path->get(); //will display "bar/leheros/troll/bar/troll"
$path->remove('troll/bar');
echo $path->getUriComponent(); //will display "/bar/leheros/troll"
~~~