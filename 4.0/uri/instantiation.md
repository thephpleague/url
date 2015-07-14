---
layout: default
title: URIs instantiation
---

# URI Instantiation

## Http, Https URI

Usually you want to work with one of the following schemes: `http`, `https`. To ease working with these schemes the library introduces the `Http` class. And because URIs come in different forms we used named constructors to offer several ways to instantiate the object.

## Instantiation

### From a string

Using the `createFromString` static method you can instantiate a new Http URI object from a string or from any object that implements the `__toString` method. Internally, the string will be parse using PHP's `parse_url` function.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$url = HttpUri::createFromString('ftp://host.example.com');
~~~

### From the server variables

Using the `createFromServer` method you can instantiate a new `League\Uri\Url` object from PHP's server variables. Of note, you must specify the `array` containing the variables usually `$_SERVER`.

~~~php
use League\Uri\Schemes\Http as HttpUri;

//don't forget to provide the $_SERVER array
$url = HttpUri::createFromServer($_SERVER);
~~~

## Generic URI Handling

The `League\Uri\Schemes\Http` class is a child object of the more generic `League\Uri\Uri` object. This object is able to manage other schemes related URI as long as they can be parse by PHP's `parse_url` function. Just like the `League\Uri\Schemes\Http`, you can extends the `League\Uri\Uri` object to create a more specified scheme related URI.

### Instantiation from parse_url results

The easiest way to instantiate a new URI object is to use its named constructors `createFromComponents` and give it the result of PHP's function `parse_url`.

~~~php
use League\Uri\Uri;
use League\Uri\Schemes\Registry;

$components = parse_url('telnet://foo.example.com');
$telnet = Uri::createFromComponents(new Registry(['telnet' => 21]), $components);
~~~

Because PHP's `parse_url` functions contains some bugs the `League\Uri\Uri` object uses a bug fixed version. So the above code should be safely rewrote using the following code:

~~~php
use League\Uri\Uri;
use League\Uri\Schemes\Registry;

$url = Uri::createFromComponents(
    new Registry(['telnet' => 23]),
    Uri::parse('telnet://foo.example.com')
);
~~~

### Instantiation from its default constructor

Of course if you already have all the required objects that implements the package interfaces, you can directly instantiate a new `League\Uri\Uri` object from them as shown below:

~~~php
use League\Uri\Uri;

$url = new Uri(
    $schemeRegistry,
    $scheme,
    $userinfo,
    $host,
    $port,
    $path,
    $query,
    $fragment
);

//where $schemeRegistry is a League\Uri\Interfaces\SchemeRegistry implementing object
//where $scheme is a League\Uri\Interfaces\Scheme implementing object
//where $user is a League\Uri\Interfaces\UserInfo implementing object
//where $host is a League\Uri\Interfaces\Host implementing interface
//where $port is a League\Uri\Interfaces\Port implementing object
//where $path is a League\Uri\Interfaces\Path implementing interface
//where $query is a League\Uri\Interfaces\Query implementing interface
//where $fragment is a League\Uri\Interfaces\Fragment implementing object
~~~

<p class="message-warning">If a new instance can not be created a <code>InvalidArgumentException</code> exception is thrown.</p>

## Scheme Registry

A [Scheme registry object](/4.0/uri/scheme-registration/) is required to enable validating the URI scheme and to detect the optional associated standard port. If the submitted scheme is invalid or is not recognized by the scheme registry an `InvalidArgumentException` exception is thrown.

~~~php
use League\Uri\Uri;
use League\Uri\Scheme\Registry;

$registry = new Registry(['telnet' => 23]);
$telnet = Uri::createFromComponents($registry, Uri::parse('telnet://foo.example.com'));

Uri::createFromComponents($registry, Uri::parse('http://www.example.com'));
//will throw an InvalidArgumentException
~~~

In the example above, a new scheme registry is created which only supports the `telnet` scheme. Thus the `League\Uri\Uri::createFromComponents` will:

- correctly instantiated a `telnet` schemed URI;
- throw an exception with an URI using the `http` scheme;

## Extending The URI object

Let say you want to create a `Mailto` class to handle mailto schemed URI. You just need to extends the <code>League\Uri\Uri</code> object and add more specific validation features to your class. Here's a quick example that you should further improve.

~~~php
namespace Example;

use League\Uri\Uri;
use League\Uri\Scheme\Registry;

class Mailto extends Uri
{
    /**
     * Validate any changes made to the URI object
     *
     * This method override the Parent isValid method
     * When it returns false an InvalidArgumentException is thrown
     *
     * @return bool
     */
    protected function isValid()
    {
        //a mailto URI does not contains any authority part
        //a mailto URI does not contains any fragment
        //a mailto URI path must be a valid email

        $auth = $this->getAuthority();
        return empty($auth)
            && $this->fragment->isEmpty()
            && filter_var(rawurldecode($this->path->__toString()), FILTER_VALIDATE_EMAIL);
    }

    /**
     * return a new object based on the submitted URI string
     *
     * @param  string $uri
     *
     * @return static
     */
    public static function createFromString($uri= '')
    {
        return static::createFromComponents(
            new Registry(['mailto' => null]),
            static::parse($uri)
        );
    }
}
~~~

And now you can easily make it work again any `mailto` scheme URI

~~~php
use Example\Mailto;

$mailto = Mailto::createFromString('mailto:boo@example.org'):
echo $mailto; //return mailto:boo@example.org
Mailto::createFromString('http://example.org'): //will throw an InvalidArgumentException
~~~

## URI Manipulation

All the properties and manipulations methods describes hereafter are available on for both the `League\Uri\Uri` and the `League\Uri\Schemes\Http` object unless otherwise explicitly stated. The main difference between both objects are in the extra validation step done when creating and updating your `http` schemed URI.

<p class="message-notice">It is possible but <strong>not recommended</strong> to manage <code>http</code> and <code>https</code> schemed URI with the <code>League\Uri\Uri</code> object.</p>

