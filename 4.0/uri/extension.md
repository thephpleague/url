---
layout: default
title: URIs extension
---

# Creating other URI objects

## Creating an URI object similar to HTTP URI

Let say you want to create a `telnet` class to handle telnet URI. You just need to extends the <code>League\Uri\Schemes\Generic\AbstractHierarchicalUri</code> object and add telnet specific validation features to your class. Here's a quick example that you should further improve.

~~~php
namespace Example;

use League\Uri\Schemes\Generic\AbstractHierarchicalUri;
use League\Uri\Interfaces\Uri;

class Telnet extends AbstractHierarchicalUri implements Uri
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
        return null === $this->fragment->getContent()
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

Of course you are free to add more methods to fulfill your own requirements. But remember that all general URI [properties](/4.0/uri/properties/) and [methods and modifiers](/4.0/uri/manipulation/) are already usable with these simple steps.

## Creating a Generic URI Object

By definition a generic URI like `mailto` or `isdn` requires more codes. For each of these schemes specific URI, the parsing and manipulating rules are differents. But nevertheless the library will help you speed up your process to create such classes. We will try to implement as quickly as possible the `mailto` scheme.

The mailto scheme URI is specific because :

- it does not have any authority part and fragment components;
- its path is made of urlencoded emails separated by a comma;
- it accepts any email header as query string parameters;

These general rules are taken from quickly reading [the mailto RFC6068](http://tools.ietf.org/html/rfc6068).

Here's how we will proceed. We will:

- create the interfaces needed <em>- helpful but not required</em>;
- implement them into concrete classes;

<p class="message-info">Using interfaces will garantee interoperability between the class we are creating and all the other league uri components.</p>

## Mailto interfaces

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

## Let's create the concrete classes

First let's write the `MailtoPath` class. Again we will use the library abstract class to speed things up. the `AbstractHierarchicalComponent` abstract class will add all manipulating methods needed. As well as all collections related methods to the class. We simply need to add the parsing method. And the method to retrieve one email.

~~~php
namespace Example;

use League\Uri\Components\AbstractHierarchicalComponent;
use InvalidArgumentException;

class MailtoPath extends AbstractHierarchicalComponent implements MailtoPathInterface
{
    use PathTrait;

    /**
     * The path separator as described in RFC6068
     *
     * Must be static to work with the named constructors methods
     */
    protected static $separator = ',';

    /**
     * New instance
     *
     * @param string $emails
     */
    protected function __construct($emails = '')
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
     *
     * @param string $str
     * @param int    $type
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

use League\Uri\Components\Fragment;
use League\Uri\Components\Host;
use League\Uri\Components\Port;
use League\Uri\Components\Query;
use League\Uri\Components\Scheme;
use League\Uri\Components\UserInfo;
use League\Uri\Interfaces\Components\Fragment as FragmentInterface;
use League\Uri\Interfaces\Components\Host as HostInterface;
use League\Uri\Interfaces\Components\Port as PortInterface;
use League\Uri\Interfaces\Components\Query as QueryInterface;
use League\Uri\Interfaces\Components\Scheme as SchemeInterface;
use League\Uri\Interfaces\Components\UserInfo as UserInfoInterface;
use League\Uri\Schemes\Generic\AbstractUri;
use League\Uri\UriParser;

class Mailto extends AbstractUri implements MailtoInterface
{
    /**
     * Create a new instance of URI
     *
     * This method override the Parent constructor method
     * And make sure the path is typehinted agaisnt the MailtoPathInterface
     *
     * @param SchemeInterface     $scheme
     * @param UserInfoInterface   $userInfo
     * @param HostInterface       $host
     * @param PortInterface       $port
     * @param MailtoPathInterface $path
     * @param QueryInterface      $query
     * @param FragmentInterface   $fragment
     */
    public function __construct(
        SchemeInterface $scheme,
        UserInfoInterface $userInfo,
        HostInterface $host,
        PortInterface $port,
        MailtoPathInterface $path,
        QueryInterface $query,
        FragmentInterface $fragment
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
        if ('mailto:' !== $this->scheme->getUriComponent()) {
            throw new InvalidArgumentException(
                'The submitted scheme is invalid for the class '.get_class($this)
            );
        }

        $expected = 'mailto:'
            .$this->path->getUriComponent()
            .$this->query->getUriComponent();

        return $this->isValidGenericUri()
            && $this->__toString() === $expected;
    }

    /**
     * Create a new instance from a string
     *
     * @param string $uri
     *
     * @return static
     */
    public static function createFromString($uri = '')
    {
        return static::createFromComponents((new UriParser())->parse($uri));
    }

    /**
     * Create a new instance from a hash of parse_url parts
     *
     * This method override the Parent constructor method
     * And make sure the path is constructed with a MailtoPath instance
     *
     * @param array $components
     *
     * @throws \InvalidArgumentException If the URI can not be parsed
     *
     * @return static
     */
    public static function createFromComponents(array $components)
    {
        $components = (new UriParser())->normalizeUriHash($components);

        return new static(
            new Scheme($components['scheme']),
            new UserInfo($components['user'], $components['pass']),
            new Host($components['host']),
            new Port($components['port']),
            new MailtoPath($components['path']),
            new Query($components['query']),
            new Fragment($components['fragment'])
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
            new Scheme('mailto'),
            new UserInfo(),
            new Host(),
            new Port(),
            MailtoPath::createFromArray($emails),
            new Query(),
            new Fragment()
        );
    }
}
~~~

Et voilà! And you can already do this:

~~~php
use Example\Mailto;

$mailto = Mailto::createFromEmails(['foo@example.com', 'info@thephpleague.com']);
$mailto->__toString(); //will return 'mailto:foo@xexample.com,info@thephpleague.com';

echo $mailto->path->getEmail(0); //returns 'foo@example.com'

var_dump($mailto->path->toArray()); //returns an array of all mails

$mailWithSubject = $mailto->mergeQuery(['subject' => 'Hello World!']);
$mailWithSUbject->__toString(); //will return 'mailto:foo@example.com,info@thephpleague.com?subject=Hello%20World%21';
~~~

There are still room for improvement by adding specific URI modifiers but I'll leave that to you to strenghen the above code.

<p class="message-info">To create hierarchical URI objects you should check the <a href="/4.0/uri/hierarchical/extension/">Hierarchical URI extension guide</a></p>