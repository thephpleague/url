---
layout: default
title: URIs extension
---

# Creating a Hierarchical URI

Let say you want to create a `telnet` class to handle telnet URI. You just need to extends the <code>League\Uri\Schemes\Generic\AbstractHierarchicalUri</code> object and add telnet specific validation features to your class. Here's a quick example that you should further improve.

~~~php
namespace Example;

use League\Uri\Schemes\Generic\AbstractHierarchicalUri;

class Telnet extends AbstractHierarchicalUri
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
        return $this->fragment->isEmpty()
            && $this->isValidGenericUri()
            && $this->isValidHierarchicalUri();
    }
}
~~~

And now you can easily make it works against any `telnet` scheme URI

~~~php
use Example\Telnet;

$uri = Telnet::createFromString('TeLnEt://example.com:23/Hello%20There'):
echo $uri; //return telnet://example.com/Hello%20There
Telnet::createFromString('http://example.org'): //will throw an InvalidArgumentException
~~~

Of course you are free to add more methods to fulfill your own requirements. But remember that all general URI [properties](/4.0/uri/properties/) and [methods](/4.0/uri/manipulation/) as well as [specific hierarchical methods](/4.0/uri/hierarchical/manipulation/) already usable with these simple steps.

To create more generic URI objects you should check the [Generic URI extension guide](/4.0/uri/generic/extension/)