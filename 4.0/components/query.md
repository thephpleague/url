---
layout: default
title: The Query Component
---

# The Query component

The library provides a `League\Uri\Query` class to ease complex query manipulation.

## Parsing and building the query string

<p class="message-warning">To preserve the query string, the library does not rely on PHP's <code>parse_str</code> and <code>http_build_query</code> functions.</p>

Instead, the `League\Uri\Query` object uses internally and exposes two public static methods that can be used to parse a query string into an array of key value pairs. And conversely creates a valid query string from the resulting array.

### Parsing the query string into an array

- `parse_str` replaces any invalid characters from the query string pair key that can not be included in a PHP variable name by an underscore `_`.
- `parse_str` merges query string values.

These behaviors, specific to PHP, may be considered to be a data loss transformation in other languages.

~~~php
$query_string = 'toto.foo=bar&toto.foo=baz';
parse_str($query_string, $arr);
// $arr is an array containing ["toto_foo" => "baz"]
~~~

To avoid these transformations, the `Query::parse` static method returns an `array` representation of the query string which preserve key/value pairs. The method expects at most 3 arguments:

- The query string;
- The query string separator, by default it is set to `&`;
- The query string encryption. It can be one of PHP constant `PHP_QUERY_RFC3986` or `PHP_QUERY_RFC1738` or `false` if you don't want any encryption. By default it is set to PHP constants `PHP_QUERY_RFC3986`

~~~php
use League\Uri\Query;

$query_string = 'toto.foo=bar&toto.foo=baz';
$arr = Query::parse($query_string, '&', PHP_RFC3986);
// $arr is an array containing ["toto.foo" => [["bar", "baz"]]
~~~

### Building the query string from an array

`http_build_query` always adds array numeric prefix to the query string even when they are not needed

using PHP's `parse_str`

~~~php
$query_string = 'foo[]=bar&foo[]=baz';
parse_str($query_string, $arr);
// $arr = ["foo" => ['bar', 'baz']];

$res = rawurldecode(http_build_query($arr, '', PHP_QUERY_RFC3986));
// $res equals foo[0]=bar&foo[1]=baz
~~~

or using `Query::parse`

~~~php
use League\Uri\Query;

$query_string = 'foo[]=bar&foo[]=baz';
$arr = Query::parse($query_string, '&', PHP_RFC3986);
// $arr = ["foo[]" => ['bar', 'baz']];

$res = rawurldecode(http_build_query($arr, '', PHP_QUERY_RFC3986));
// $res equals foo[][0]=bar&oo[][1]=baz
~~~

The `Query::build` static method returns and preserves string representation of the query string from the `Query::parse` array result. the method expects at most 3 arguments:

- A valid `array` of data to convert;
- The query string separator, by default it is set to `&`;
- The query string encryption. It can be one of PHP constant `PHP_QUERY_RFC3986` or `PHP_QUERY_RFC1738` or `false` if you don't want any encryption. By default it is set to PHP constants `PHP_QUERY_RFC3986`

~~~php
use League\Uri\Query;

$query_string = 'foo[]=bar&foo[]=baz';
$arr = Query::parse($query_string, '&', PHP_RFC3986);
var_export($arr);
// $arr include the following data ["foo[]" => ['bar', 'baz']];

$res = Query::build($arr, '&', false);
// $res equals 'foo[]=bar&foo[]=baz'
~~~

No key indexes is added and the query string is safely recreated

## Query creation

### Using the default constructor

A new `League\Uri\Query` object can be instantiated using its the default constructor.

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=yolo&z=');
echo $query; //display 'foo=bar&p=yolo&z'
~~~

<p class="message-warning">When using the default constructor do not prepend your query delimiter to the string as it will be considered as part of the first parameter name.</p>

<p class="message-warning">If the submitted value is not a valid query an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a League\Uri\Url object

~~~php
use League\Uri\Url;

$url   = Url::createFromString('http://url.thephpleague.com/path/to/here?foo=bar');
$query = $url->query; // $query is a League\Uri\Query object;
~~~

### Using a named constructor

It is possible to create a `Query` object using an `array` or a `Traversable` object with the `Query::createFromArray` method. The submitted data must provide an array which preserved key/value pairs similar to the result of `Query::parse`.

- If a given parameter value is `null` it will be rendered without any value in the resulting query string;
- If a given parameter value is an empty string il will be rendered without any value **but** with a `=` sign appended to it;

~~~php
use League\Uri\Query;

$query =  Query::createFromArray(['foo' => 'bar', 'p' => 'yolo', 'z' => '']);
echo $query; //display 'foo=bar&p=yolo&z='

$query =  Query::createFromArray(['foo' => 'bar', 'p' => null, 'z' => '']);
echo $query; //display 'foo=bar&p&z='
~~~

## Query representations

### String representation

Basic query representations is done using the following methods:

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->__toString();      //return 'foo=bar&p=y%20olo&z'
$query->getUriComponent(); //return '?foo=bar&p=y%20olo&z'
~~~

### Array representation

A query can be represented as an array of its internal parameters. Through the use of the `Query::toArray` method the class returns the object array representation. This method uses `Query::parse` to create the array.

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->toArray();
// returns [
//     'foo' => 'bar',
//     'p'   => 'y olo',
//     'z'   => '',
// ]
~~~

The array returned by `toArray` differs from the one returned by `parse_str` has it preserves the query string values.

## Accessing Query content

### Countable and IteratorAggregate

The class provides several methods to works with its parameters. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of parameters and use the `foreach` construct to iterate overs them.

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=y+olo&z=');
count($query); //return 4
foreach ($query as $parameter => $value) {
    //do something meaningful here
}
~~~

### Parameter name

If you are interested in getting all the parameters names you can do so using the `Query::keys` method like show below:

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->keys();        //return ['foo', 'p', 'z'];
$query->keys('bar');   //return ['foo'];
$query->keys('gweta'); //return [];
~~~

The methods returns all the parameters name, but if you supply an argument, only the parameters name whose value equals the argument are returned.

If you want to be sure that a parameter name exists before using it you can do so using the `Query::hasKey` method which returns `true` if the submitted parameter name exists in the current object.

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->hasKey('p');    //return true
$query->hasKey('john'); //return false
~~~

### Parameter value

If you are only interested in a given parameter you can access it directly using the `Query::getValue` method as show below:

~~~php
use League\Uri\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->getValue('foo');          //return 'bar'
$query->getValue('gweta');        //return null
$query->getValue('gweta', 'now'); //return 'now'
~~~

The method returns the value of a specific parameter name. If the offset does not exists it will return the value specified by the second argument which default to `null`.

## Modifying a query

<p class="message-notice">If the modifications do not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

### Sort parameters

Sometimes you may wish to sort your query. To do so, you can use the `Query::ksort` method. This method expects a single argument which can be:

One of PHP's sorting constant used by the [sort function](http://php.net/sort). **In this case the query parameters are sorted from low to hight** like PHP's [ksort function](http://php.net/ksort)

~~~php
use League\Uri\Query;

$query    = new Query('foo=bar&baz=toto');
$newQuery = $query->ksort(SORT_STRING);
$newQuery->__toString(); //return baz=toto&foo=bar
~~~

A user-defined comparison function which must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second, like PHP's [uksort function](http://php.net/uksort)

~~~php
use League\Uri\Query;


$query    = new Query('foo=bar&baz=toto');
$newQuery = $query->ksort('strcmp');
$newQuery->__toString(); //return baz=toto&foo=bar
~~~

<p class="message-notice">This method is used by the <code>League\Uri\Url::ksortQuery</code> method</p>

### Add or Update parameters

If you want to add or update the query parameters you need to use the `Query::merge` method. This method expects a single argument. This argument can be:

A string or a stringable object:

~~~php
use League\Uri\Query;

$query    = new Query('foo=bar&baz=toto');
$newQuery = $query->merge('foo=jane&r=stone');
$newQuery->__toString(); //return foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

An `array` or a `Traversable` object similar to the result of the `Query::parse` method:

~~~php
use League\Uri\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $query->merge(['foo' => 'jane', 'r' => 'stone']);
$newQuery->__toString(); //return foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

Another `Query` object

~~~php
use League\Uri\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $query->merge(new Query('foo=jane&r=stone'));
$newQuery->__toString(); //return foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

<p class="message-notice">Values equal to <code>null</code> or the empty string are merge differently.</p>

~~~php
use League\Uri\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $alt->merge(['foo' => 'jane', 'baz' => '', 'r' => null]);
$newQuery->__toString(); //return foo=jane&baz=&r
// the 'foo' parameter was updated
// the 'r' parameter was added without any value
// the 'baz' parameter was updated to an empty string and its = sign remains
~~~

<p class="message-notice">This method is used by the <code>League\Uri\Url::mergeQuery</code> method</p>

### Remove parameters

To remove parameters from the current object and returns a new `Query` object without them you must use the `Query::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Uri\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(['foo', 'p']);
echo $newQuery; //displays 'z='
~~~

Or a callable that will select the list of parameter names to remove.

~~~php
use League\Uri\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(function ($value) {
	return strpos($value, 'p') === false;
});
echo $newQuery; //displays 'p=y+olo';
~~~

<p class="message-notice">This method is used by the <code>League\Uri\Url::withoutQueryValues</code> method</p>

### Filter the Query

Another way to select parameters from the query  object is to filter them.

You can filter the query according to its parameters name or value using the `Query::filter` method.

The first parameter must be a `callable`

~~~php
use League\Uri\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->filter(function ($value) {
	return ! empty($value);
});
echo $newQuery; //displays 'foo=bar&p=y+olo'
~~~

By specifying the second argument flag you can change how filtering is done:

- use `Query::FILTER_USE_VALUE` to filter according to the query parameter value;
- use `Query::FILTER_USE_KEY` to filter according to the query parameter name;

By default, if no flag is specified the method will filter by value.

~~~php
use League\Uri\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->filter(function ($value) {
	return $value != 'foo';
}, Query::FILTER_USE_KEY);
echo $newQuery; //displays 'p=y%20olo&z='
~~~

<p class="message-notice">This method is used by the <code>League\Uri\Url::filterQuery</code> method</p>