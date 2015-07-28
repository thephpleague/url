---
layout: default
title: URIs instantiation
---

# URI Handling

## URI instantiation

To ease URI instantiation, and because URIs come in different forms we used named constructors to offer several ways to instantiate the object.

<p class="message-warning">If a new instance can not be created a <code>InvalidArgumentException</code> exception is thrown.</p>

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

Even thought it is possible, it is not recommend to instantiate any URI object using the default constructor. Since every URI may be instantiated differently. It is easier to always use the documentated named constructors.

## Generic URI Handling

Out of the box the library provides the following specialized classes:

- `League\Uri\Schemes\Data` which deals with [Data URI](/4.0/uri/datauri/);
- `League\Uri\Schemes\Ftp` which deals with the [FTP scheme specific URI](/4.0/uri/ftp/);
- `League\Uri\Schemes\Http` which deals with [HTTP and HTTPS scheme specific URI](/4.0/uri/http/);
- `League\Uri\Schemes\Ws` which deals with [WS and WSS (websocket) scheme specific URI](/4.0/uri/ws/);

But you can easily create your own class to manage others scheme specific URI.

Let say you want to create a `telnet` class to handle telnet URI. You just need to extends the <code>League\Uri\Schemes\Generic\AbstractHierarchicalUri</code> object and add telnet specific validation features to your class. Here's a quick example that you should further improve.

~~~php
namespace Example;

use League\Uri\Schemes\Generic\AbstractHierarchical;

class Telnet extends AbstractHierarchical
{
    /**
     * Supported Schemes with their associated port
     *
     * This property override the Parent supportedSchemes empty array
     *
     * @var array
     */
    protected static $supportedSchemes = [
        'telnet' => 23,
    ];

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
        if (!$this->fragment->isEmpty()) {
            return false;
        }

        return $this->isValidHierarchicalUri();
    }
}
~~~

And now you can easily make it works again any `telnet` scheme URI

~~~php
use Example\Telnet;

$uri = Telnet::createFromString('TeLnEt://example.com:23/Hello%20There'):
echo $uri; //return telnet://example.com/Hello%20There
Telnet::createFromString('http://example.org'): //will throw an InvalidArgumentException
~~~

Of course you are free to add more methods to fulfill your own requirements.