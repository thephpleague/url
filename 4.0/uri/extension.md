---
layout: default
title: URIs extension
---

# Generic URI class creation

Out of the box the library provides the following specialized classes:

- `League\Uri\Schemes\Data` which deals with [Data URI](/4.0/uri/datauri/);
- `League\Uri\Schemes\Ftp` which deals with the [FTP scheme specific URI](/4.0/uri/ftp/);
- `League\Uri\Schemes\Http` which deals with [HTTP and HTTPS scheme specific URI](/4.0/uri/http/);
- `League\Uri\Schemes\Ws` which deals with [WS and WSS (websocket) scheme specific URI](/4.0/uri/ws/);

But you can easily create your own class to manage others scheme specific URI.

## Creating a Hierarchical URI

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
        if (!$this->fragment->isEmpty()) {
            return false;
        }

        return $this->isValidHierarchicalUri();
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

Of course you are free to add more methods to fulfill your own requirements. But remember that all general URI and Hierarchical URI [properties](/4.0/uri/properties/) and [methods](/4.0/uri/manipulation/) are already usable with these simple steps.

## Creating a Opaque URI

By definition Opaque URI like `mailto` or `isdn` requires more codes. For each of these schemes specific URI, the parsing and manipulating rules are differents. But nevertheless the library will help you speed up your process to create such classes. We will try to implement as quickly as possible the `mailto` scheme.

The mailto scheme URI is specific because :

- it does not have any authority part and fragment components;
- its path is made of urlencoded emails separated by a comma;
- it accepts any email header as query string parameters;

These general rules are taken from quickly reading [the mailto RFC6068](http://tools.ietf.org/html/rfc6068). 

Before coding anything we should:

- create the interfaces needed;
- implement them into concrete classes;

Using interfaces will garantee interoperability between the class we are creating and all the other league uri components.

### Mailto interfaces

The mail specific area of the `mailto` scheme URI is the path. It only contains valid emails separated by a comma as per RFC specification. It means we need an interface to manipulate the path as a collection of emails. So we can remove/append/prepend/replace emails as we want. As a matter a fact there's already a interface for that in the library. To complete this interface we just need one method to retrieve one specific email from the path based on its index, all the other methods are already specify by the other interfaces.

~~~php
namespace Example;

use League\Uri\Interfaces\Components\Path;
use League\Uri\Interfaces\Components\HierarchicalComponent;

interface MailtoPathInterface extends Path, HierarchicalComponent
{
    /**
     * Retrieves a single host label.
     *
     * Retrieves a single host label. If the label offset has not been set,
     * returns the default value provided.
     *
     * @param string $offset  the label offset
     * @param mixed  $default Default value to return if the offset does not exist.
     *
     * @return mixed
     */
    public function getEmail($offset, $default = null);
}
~~~

<p class="message-notice">It is important that the <code>MailtoPathInterface</code> extends the package <code>Path</code> interface too to make the class work as expected</p>

Now we want create a specific interface for the Mailto uri

~~~php
namespace Example;

use League\Uri\Interfaces\Schemes\Uri;

interface MailtoInterface extends Uri
{
    public function appendEmail($email);

    public function prependEmail($email);

    public function replaceEmail($email);

    ...
}
~~~

You can add other methods if you want.

<p class="message-notice">Again here the key feature is to extends league interface to be able to quickly build a robust <code>Mailto</code> class.</p>

### Let's create the concrete classes

First let's write the `MailtoPath` class. Again we will use the library abstract class to speed things up. the `AbstractHierarchicalComponent` abstract class will add all manipulating methods needed. As well as all collections related methods to the class. We simply need to add the parsing method. And the method to retrieve one email.

~~~php
namespace Example;

use League\Uri\Components\AbstractHierarchicalComponent;
use InvalidArgumentException;

class MailtoPath extends AbstractHierarchicalComponent implements MailtoPathInterface
{
    /**
     * {@inheritdoc}
     */
    protected $data = [];

    /**
     * The path separator as described in RFC6068
     */
    protected static $separator = ',';

    /**
     * validate the path string
     * This method is called when a manipulation method is applied
     * to validate the resulting manipulation
     */
    protected function init($emails)
    {
        $emails = array_map('rawurldecode', explode(static::$separator, $emails));
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails);
        if (empty($emails)) {
            return;
        }
        $verif = filter_var($emails, FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_ARRAY);
        if ($emails !== $verif) {
            throw new InvalidArgumentException('the submitted path is invalid');
        }
        $this->data = $emails;
    }

    /**
     * format the string before manipulation methods
     * not needed in case of a Opaque URI
     */
    protected static function formatComponentString($str, $type)
    {
        return $str;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * return the string representation of the path
     */
    public function __toString()
    {
        return implode(static::$separator, array_map('rawurlencode', $this->data));
    }
}
~~~

Now let's do the same for the main `Mailto` class. This time we will built by extending the <code>League\Uri\Schemes\Generic\AbstractUri</code> object by adding `mailto` specific validation rules to the class.

~~~php
namespace Example;

use League\Uri\Components;
use League\Uri\Interfaces;
use League\Uri\Schemes\Generic\AbstractUri;

class Mailto extends AbstractUri implements MailtoInterface
{
    /**
     * Create a new instance of URI
     *
     * @param Interfaces\Components\Scheme   $scheme
     * @param Interfaces\Components\UserInfo $userInfo
     * @param Interfaces\Components\Host     $host
     * @param Interfaces\Components\Port     $port
     * @param MailtoPathInterface            $path
     * @param Interfaces\Components\Query    $query
     * @param Interfaces\Components\Fragment $fragment
     */
    public function __construct(
        Interfaces\Components\Scheme $scheme,
        Interfaces\Components\UserInfo $userInfo,
        Interfaces\Components\Host $host,
        Interfaces\Components\Port $port,
        MailtoPathInterface $path,
        Interfaces\Components\Query $query,
        Interfaces\Components\Fragment $fragment
    ) {
        $this->scheme = $scheme;
        $this->userInfo = $userInfo;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
        $this->assertValidObject();
    }

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
        return $this->__toString() === 'mailto:'.$this->path->getUriComponent().this->query->getUriComponent();
    }

    /**
     * Create a new instance from a hash of parse_url parts
     *
     * @param array $components
     *
     * @throws \InvalidArgumentException If the URI can not be parsed
     *
     * @return static
     */
    public static function createFromComponents(array $components)
    {
        $components = static::formatComponents($components);
        return new static(
            new Components\Scheme($components['scheme']),
            new Components\UserInfo($components['user'], $components['pass']),
            new Components\Host($components['host']),
            new Components\Port($components['port']),
            new MailtPath($components['path']),
            new Components\Query($components['query']),
            new Components\Fragment($components['fragment'])
        );
    }

    /**
     * A specific named constructor to speed up
     * creating a new instance from a collection of mails
     *
     * @param  \Traversable|array $emails
     *
     * @throws \InvalidArgumentException If the URI can not be parsed
     *
     * @return static
     */
    public static function createFromEmails($emails)
    {
        return new static(
            new Components\Scheme('mailto'),
            new Components\UserInfo(),
            new Components\Host(),
            new Components\Port(),
            MailtPath::createFromArray($emails),
            new Components\Query(),
            new Components\Fragment()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function replaceEmail($offset, $email)
    {
        return $this->withProperty('path', $this->path->replace($offset, $email));
    }

    /**
     * {@inheritdoc}
     */
    public function appendEmail($email)
    {
        return $this->withProperty('path', $this->path->append($email));
    }

    /**
     * {@inheritdoc}
     */
    public function prependEmail($email)
    {
        return $this->withProperty('path', $this->path->prepend($email));
    }
}
~~~

Et voilà! And you can already do this:

~~~php
use Example\Mailto;

$mailto = Mailto::createFromEmails(['foo@example.com', 'info@thephpleague.com']);
$mailto->__toString(); //will return 'mailto:foo@xexample.com,info@thephpleague.com';

echo $mailto->path->getEmail(0); //returns 'foo@example.com'

$newEmail = $mailto->appendEmail('greg@theguy.com');
$newEmail->__toString(); //will return 'mailto:foo@example.com,info@thephpleague.com,greg@theguy.com';

$mailWithSubject = $mailto->mergeQuery(['subject' => 'Hello World!']);
$mailWithSUbject->__toString(); //will return 'mailto:foo@example.com,info@thephpleague.com?subject=Hello%20World%21';
~~~

Just like with the `telnet` class there are still room for improvement. But more than a basic idea is presented in the above examples.