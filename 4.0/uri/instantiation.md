---
layout: default
title: URIs instantiation
---

# URI Handling

## URI instantiation

To ease URI instantiation, and because URIs come in different forms we used named constructors to offer several ways to instantiate the object.

### From a string

Using the `createFromString` static method you can instantiate a new URI object from a string or from any object that implements the `__toString` method. Internally, the string will be parse using PHP's `parse_url` function.

~~~php
use League\Uri\Schemes\Ftp as FtpUri;

$uri = FtpUri::createFromString('ftp://host.example.com/path/to/image.png;type=i');
~~~

### From parse_url results

You can also instantiate a new URI object using the `createFromComponents` named constructor by giving it the result of PHP's function `parse_url`.

~~~php
use League\Uri\Schemes\Ws as WsUri;

$components = parse_url('wss://foo.example.com/path/to/index.php?param=value');

$uri = WsUri::createFromComponents($components);
~~~

Because PHP's `parse_url` functions contains some bugs the `Uri` object uses a bug fixed version. So the above code should be safely rewrote using the following code:

~~~php
use League\Uri\Schemes\Ws as WsUri;

$components = WsUri::parse('wss://foo.example.com/path/to/index.php?param=value');

$uri = HttpUri::createFromComponents($components);
~~~

### Instantiation from its default constructor

Of course if you already have all the required objects that implements the package interfaces, you can directly instantiate a new `Uri` object from the default constructor as shown below:

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = new HttpUri(
    $scheme,
    $userinfo,
    $host,
    $port,
    $path,
    $query,
    $fragment
);

//where $scheme is a League\Uri\Interfaces\Scheme implementing object
//where $user is a League\Uri\Interfaces\UserInfo implementing object
//where $host is a League\Uri\Interfaces\Host implementing interface
//where $port is a League\Uri\Interfaces\Port implementing object
//where $path is a League\Uri\Interfaces\Path implementing interface
//where $query is a League\Uri\Interfaces\Query implementing interface
//where $fragment is a League\Uri\Interfaces\Fragment implementing object
~~~

<p class="message-warning">If a new instance can not be created a <code>InvalidArgumentException</code> exception is thrown.</p>

## Generic URI Handling

Out of the box the library provides the following specialized classes:

- `League\Uri\Schemes\Ftp` which deals with the [FTP scheme specific URI](/4.0/uri/ftp/);
- `League\Uri\Schemes\Http` which deals with [HTTP and HTTPS scheme specific URI](/4.0/uri/http/);
- `League\Uri\Schemes\Ws` which deals with [WS and WSS (websocket) scheme specific URI](/4.0/uri/ws/);

But you can easily create your own class to manage others scheme specific URI.

Let say you want to create a `Mailto` class to handle mailto schemed URI. You just need to extends the <code>League\Uri\Schemes\AbstractUri</code> object and add mailto specific validation features to your class. Here's a quick example that you should further improve.

~~~php
namespace Example;

use League\Uri\Schemes\AbstractUri;

class Mailto extends AbstractUri
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
     * A mailto specific URI has no standard Port
     *
     * @return bool
     */
    public function hasStandardPort()
    {
        return false;
    }

    /**
     * return a new object based on the submitted email
     *
     * @param string $email
     *
     * @return static
     */
    public static function createFromEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('the submitted email is invalid');
        }

        return static::createFromString('mailto:'.rawurlencode($email));
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

All the properties and manipulations methods describes hereafter are available on all URI object unless explicitly stated.
