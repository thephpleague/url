---
layout: default
title: The Query Component
---

# The Query component

The library proves a `League\Url\Query` class to ease complex query manipulation.

## Query creation

### Using the default constructor

Just like any other component, a new `League\Url\Query` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=yolo&z=');
echo $query; //display 'foo=bar&p=yolo&z'
~~~

<p class="message-warning">When using the default constructor do not prepend your query delimiter to the string as it will be considered as part of the first parameter name.</p>

<p class="message-warning">If the submitted value is not a valid query an <code>InvalidArgumentException</code> will be thrown.</p>

<p class="message-warning">To preserve parameters keys and value, the method does not uses PHP <code>parse_str</code> function.</p>

### Using a League\Url object

~~~php
use League\Url\Url;

$url  = Url::createFromUrl('http://url.thephpleague.com/path/to/here?foo=bar');
$query = $url->query; // $query is a League\Url\Query object;
~~~

### Using a named constructor

It is possible to create a `Query` object using an array or a `Traversable` object with the `Query::createFromArray` method. The expected array must not contain any nested array.

<p class="message-warning">The method does not uses PHP <code>http_build_query</code> as this methods corrupt key parameters.</p>

- If a given parameter value is `null` it will be rendered without any value in the resulting query string;
- If a given parameter value is an empty string il will be rendered without any value **but** with a `=` sign appended to it;

~~~php
use League\Url\Query;

$query =  Query::createFromArray(['foo' => 'bar', 'p' => 'yolo', 'z' => '']);
echo $query; //display 'foo=bar&p=yolo&z='

$query =  Query::createFromArray(['foo' => 'bar', 'p' => null, 'z' => '']);
echo $query; //display 'foo=bar&p&z='
~~~

## Query representations

### String representation

Basic query representations is done using the following methods:

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->__toString();      //return 'foo=bar&p=y%20olo&z'
$query->getUriComponent(); //return '?foo=bar&p=y%20olo&z'
$query->format('&amp;', PHP_QUERY_RFC3986);  //return '?foo=bar&amp;p=y%20olo&z'
~~~

The added `Query::format` method helps you format your query differently. The method accepts two parameters:

- `$separator` which is the query separator sequence;
- `$enc_type` which is the query encoding mechanism. This parameter expects one of PHP query constants (ie: `PHP_QUERY_RFC3986` or the older `PHP_QUERY_RFC1738`).

### Array representation

A query can be represented as an array of its internal parameters. Through the use of the `Query::toArray` method the class returns the object array representation.

~~~php
use League\Url\Query;

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
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
count($query); //return 4
foreach ($query as $parameter => $value) {
    //do something meaningful here
}
~~~

### Parameter name

If you are interested in getting all the parametes name you can do so using the `Query::offsets` method like show below:

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->offsets();        //returns ['foo', 'p', 'z'];
$query->offsets('bar');   //returns ['foo'];
$query->offsets('gweta'); //returns [];
~~~

The methods returns all the parameters name, but if you supply an argument, only the parameters name whose value equals the argument are returned.

If you want to be sure that a parameter name exists before using it you can do so using the `Query::hasOffset` method which returns `true` if the submitted parameter name exists in the current object.

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->hasOffset('p');    //returns true
$query->hasOffset('john'); //returns false
~~~

### Parameter value

If you are only interested in a given parameter you can access it directly using the `Query::getParameter` method as show below:

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->getParameter('foo');          //returns 'bar'
$query->getParameter('gweta');        //returns null
$query->getParameter('gweta', 'now'); //returns 'now'
~~~

The method returns the value of a specific parameter name. If the offset does not exists it will return the value specified by the second argument which default to `null`.

## Modifying a query

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

### Add or Update parameters

If you want to add or update the query parameters you need to use the `Query::merge` method. This method expects a single argument in form of an `array` or a `Traversable` object.

~~~php
use League\Url\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $alt->merge(['foo' => 'jane', 'r' => 'stone']);
$newQuery->__toString(); //returns foo=jane&baz=toto&r=stone
// the 'foo' parameter was updated
// the 'r' parameter was added
~~~

<p class="message-notice">Parameters whose value equals <code>null</code> or an empty string are merge differently.</p>

~~~php
use League\Url\Query;

$query    = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$newQuery = $alt->merge(['foo' => 'jane', 'baz' => '', 'r' => null]);
$newQuery->__toString(); //returns foo=jane&baz=&r
// the 'foo' parameter was updated
// the 'r' parameter was added without any value
// the 'baz' parameter was updated to an empty string and its = sign remains
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::mergeQueryParameters</code></p>

### Remove parameters

To remove parameters from the current object and returns a new `Query` object without them you must use the `Query::without` method. This method expects a single argument.

This argument can be an array containing a list of parameter names to remove.

~~~php
use League\Url\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(['foo', 'p']);
echo $newQuery; //displays 'z'
~~~

Or a callable that will select the list of parameter names to remove.

~~~php
use League\Url\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(function ($value) {
	return strpos($value, 'p') === false;
});
echo $newQuery; //displays 'p=y+olo';
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::withoutQueryParameters</code></p>

### Filter the Query

Another way to select parameters from the query  object is to filter them.

You can filter the query according to its parameters name or value using the `Query::filter` method.

The first parameter must be a `callable`

~~~php
use League\Url\Query;

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
use League\Url\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->filter(function ($value) {
	return $value != 'foo';
}, Query::FILTER_USE_KEY);
echo $newQuery; //displays 'p=y%20olo&z='
~~~

<p class="message-notice">This method is used by the <code>League\Url\Url</code> class as <code>Url::filterQuery</code></p>