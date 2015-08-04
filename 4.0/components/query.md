---
layout: default
title: The Query Component
---

# The Query component

The library provides a `League\Uri\Components\Query` class to ease complex query manipulation.

## Query creation

### Using the default constructor

A new `League\Uri\Components\Query` object can be instantiated using its the default constructor.

~~~php
use League\Uri\Components\Query;

$query = new Query('foo=bar&p=yolo&z=');
echo $query; //display 'foo=bar&p=yolo&z'
~~~

<p class="message-warning">When using the default constructor do not prepend your query delimiter to the string as it will be considered as part of the first parameter name.</p>

<p class="message-warning">If the submitted value is not a valid query an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a League Uri object

~~~php
use League\Uri\Ws as WsUri;

$uri = WsUri::createFromComponents(
    parse_url('wss://url.thephpleague.com/path/to/here?foo=bar')
);
$query = $uri->query; //$query is a League\Uri\Components\Query object;
~~~

### Using a named constructor

It is possible to create a `Query` object using an `array` or a `Traversable` object with the `Query::createFromArray` method. The submitted data must provide an array which preserved key/value pairs similar to the result of [Parser::parseQuery](/4.0/services/parser/#parsing-the-query-string-into-an-array).

- If a given parameter value is `null` it will be rendered without any value in the resulting query string;
- If a given parameter value is an empty string il will be rendered without any value **but** with a `=` sign appended to it;

~~~php
use League\Uri\Components\Query;

$query =  Query::createFromArray(['foo' => 'bar', 'p' => 'yolo', 'z' => '']);
echo $query; //display 'foo=bar&p=yolo&z='

$query =  Query::createFromArray(['foo' => 'bar', 'p' => null, 'z' => '']);
echo $query; //display 'foo=bar&p&z='
~~~

## Query representations

### String representation

Basic query representations is done using the following methods:

~~~php
use League\Uri\Components\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->__toString();      //return 'foo=bar&p=y%20olo&z'
$query->getUriComponent(); //return '?foo=bar&p=y%20olo&z'
~~~

### Array representation

A query can be represented as an array of its internal parameters. Through the use of the `Query::toArray` method the class returns the object array representation. This method uses `Parser::parseQuery` to create the array.

~~~php
use League\Uri\Components\Query;

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
use League\Uri\Components\Query;

$query = new Query('foo=bar&p=y+olo&z=');
count($query); //return 4
foreach ($query as $parameter => $value) {
    //do something meaningful here
}
~~~

### Parameter name

If you are interested in getting all the parameters names you can do so using the `Query::keys` method like show below:

~~~php
use League\Uri\Components\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->keys();        //return ['foo', 'p', 'z'];
$query->keys('bar');   //return ['foo'];
$query->keys('gweta'); //return [];
~~~

The methods returns all the parameters name, but if you supply an argument, only the parameters name whose value equals the argument are returned.

If you want to be sure that a parameter name exists before using it you can do so using the `Query::hasKey` method which returns `true` if the submitted parameter name exists in the current object.

~~~php
use League\Uri\Components\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->hasKey('p');    //return true
$query->hasKey('john'); //return false
~~~

### Parameter value

If you are only interested in a given parameter you can access it directly using the `Query::getValue` method as show below:

~~~php
use League\Uri\Components\Query;

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
use League\Uri\Components\Query;

$query    = new Query('foo=bar&baz=toto');
$newQuery = $query->ksort(SORT_STRING);
$newQuery->__toString(); //return baz=toto&foo=bar
~~~

A user-defined comparison function which must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second, like PHP's [uksort function](http://php.net/uksort)

~~~php
use League\Uri\Components\Query;


$query    = new Query('foo=bar&baz=toto');
$newQuery = $query->ksort('strcmp');
$newQuery->__toString(); //return baz=toto&foo=bar
~~~

<p class="message-notice">This method is used by the Hierarchical URI <code>ksortQuery</code> method</p>

### Add or Update parameters

If you want to add or update the query parameters you need to use the `Query::merge` method. This method expects a single argument. This argument can be:

A string or a stringable object:

~~~php
use League\Uri\Components\Query;

$query    = new Query('foo=bar&baz=toto');
$newQuery = $query->merge('foo=jane&r=stone');
$newQuery->__toString(); //return foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

An `array` or a `Traversable` object similar to the result of the `Parser::parseQuery` method:

~~~php
use League\Uri\Components\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $query->merge(['foo' => 'jane', 'r' => 'stone']);
$newQuery->__toString(); //return foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

Another `Query` object

~~~php
use League\Uri\Components\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $query->merge(new Query('foo=jane&r=stone'));
$newQuery->__toString(); //return foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

<p class="message-notice">Values equal to <code>null</code> or the empty string are merge differently.</p>

~~~php
use League\Uri\Components\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $alt->merge(['foo' => 'jane', 'baz' => '', 'r' => null]);
$newQuery->__toString(); //return foo=jane&baz=&r
// the 'foo' parameter was updated
// the 'r' parameter was added without any value
// the 'baz' parameter was updated to an empty string and its = sign remains
~~~

<p class="message-notice">This method is used by the Hierarchical URI <code>mergeQuery</code> method</p>

### Remove parameters

To remove parameters from the current object and returns a new `Query` object without them you must use the `Query::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Uri\Components\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(['foo', 'p']);
echo $newQuery; //displays 'z='
~~~

Or a callable that will select the list of parameter names to remove.

~~~php
use League\Uri\Components\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(function ($value) {
	return strpos($value, 'p') === false;
});
echo $newQuery; //displays 'p=y+olo';
~~~

<p class="message-notice">This method is used by the Hierarchical URI <code>withoutQueryValues</code> method</p>

### Filter the Query

Another way to select parameters from the query  object is to filter them.

You can filter the query according to its parameters name or value using the `Query::filter` method.

The first parameter must be a `callable`

~~~php
use League\Uri\Components\Query;

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
use League\Uri\Components\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->filter(function ($value) {
	return $value != 'foo';
}, Query::FILTER_USE_KEY);
echo $newQuery; //displays 'p=y%20olo&z='
~~~

<p class="message-notice">This method is used by the Hierarchical URI <code>filterQuery</code> method</p>