---
layout: layout
title: The Path class
---

# The Path class

This [multiple values component class](/components/overview/#complex-components) manage the URL path component by implementing the `League\Url\Components\SegmentInterface`. 

The `League\Url\Components\SegmentInterface` interface adds the following method:

* `append($data, $whence = null, $whence_index = null)`: append data into the component;
* `prepend($data, $whence = null, $whence_index = null)`: prepend data into the component;
* `remove($data)`: remove data from the component;

The arguments:

* The `$data` argument can be `null`, a valid component string, a object implementing the `__toString` method, an array or a `Traversable` object;
* The `$whence` argument specify the string segment where to include the data;
* The `$whence_index` argument specify the `$whence` index if it is present more than once. The value starts at `0`;
* When using the `remove` method, if the pattern is present multiple times only the first match found is removed 

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
//if you do not insert you data around the first occurence
//the $whence_index start at 0
$path->prepend('bar', 'troll', 1);
echo $path->get(); //will display "bar/leheros/troll/bar/troll"
$path->remove('troll/bar');
echo $path->getUriComponent(); //will display "/bar/leheros/troll"
~~~