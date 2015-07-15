<?php
/**
* This file is part of the League.url library
*
* @license http://opensource.org/licenses/MIT
* @link https://github.com/thephpleague/url/
* @version 3.3.5
* @package League.url
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace League\Url;

use RuntimeException;

/**
 * A Factory to ease League\Url\Url Object instantiation
 *
 *  @package League.url
 *  @since  3.0.0
 */
abstract class AbstractUrl implements UrlInterface
{
    /**
    * Scheme
    *
    * @var Components\Scheme
    */
    protected $scheme;

    /**
    * User
    *
    * @var Components\User
    */
    protected $user;

    /**
    * Pass
    *
    * @var Components\Pass
    */
    protected $pass;

    /**
     * Host
     *
     * @var Components\Host
     */
    protected $host;

    /**
     * Port
     *
     *@var Components\Port
     */
    protected $port;

    /**
     * Path
     *
     * @var Components\Path
     */
    protected $path;

    /**
     * Query
     *
     * @var Components\Query
     */
    protected $query;

    /**
     * Fragment
     *
     * @var Components\Fragment
     */
    protected $fragment;

    /**
     * Tell whether PHP native parse_url is buggy
     *
     * @var bool
     */
    protected static $is_parse_url_bugged;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $url = $this->getBaseUrl().$this->getRelativeUrl();
        if ('/' == $url) {
            return '';
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativeUrl(UrlInterface $ref_url = null)
    {
        if (is_null($ref_url)) {
            return $this->path->getUriComponent()
                .$this->query->getUriComponent()
                .$this->fragment->getUriComponent();
        }

        if ($this->getBaseUrl() != $ref_url->getBaseUrl()) {
            return $this->__toString();
        }

        return $this->path->getRelativePath($ref_url->getPath())
            .$this->query->getUriComponent()
            .$this->fragment->getUriComponent();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        $user = $this->user->getUriComponent().$this->pass->getUriComponent();
        if ('' != $user) {
            $user .= '@';
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        $user = $this->getUserInfo();
        $host = $this->host->getUriComponent();
        $port = $this->port->getUriComponent();

        return $user.$host.$port;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        $scheme = $this->scheme->getUriComponent();
        $auth = $this->getAuthority();
        if ('' != $auth && '' == $scheme) {
            $scheme = '//';
        }

        return $scheme.$auth;
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(UrlInterface $url)
    {
        return $this->__toString() == $url->__toString();
    }

    /**
     * Retuns a array representation like parse_url
     * But includes all components
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'scheme' => $this->scheme->get(),
            'user' => $this->user->get(),
            'pass' => $this->pass->get(),
            'host' => $this->host->get(),
            'port' => $this->port->get(),
            'path' => $this->path->get(),
            'query' => $this->query->get(),
            'fragment' => $this->fragment->get(),
        );
    }

    /**
     * Return a instance of Url from a string
     *
     * @param string $url a string or an object that implement the __toString method
     *
     * @return static
     *
     * @throws RuntimeException If the URL can not be parse
     */
    public static function createFromUrl($url)
    {
        $url = (string) $url;
        $url = trim($url);
        $original_url = $url;
        $url = self::sanitizeUrl($url);

        //if no valid scheme is found we add one
        if (is_null($url)) {
            throw new RuntimeException(sprintf('The given URL: `%s` could not be parse', $original_url));
        }

        $components = array_merge(array(
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ), self::parseUrl($url));
        $components = self::formatAuthComponent($components);
        $components = self::formatPathComponent($components, $original_url);

        return new static(
            new Components\Scheme($components['scheme']),
            new Components\User($components['user']),
            new Components\Pass($components['pass']),
            new Components\Host($components['host']),
            new Components\Port($components['port']),
            new Components\Path($components['path']),
            new Components\Query($components['query']),
            new Components\Fragment($components['fragment'])
        );
    }

    /**
     * Parse a string as an URL
     *
     * @param  string $url The URL to parse
     *
     * @throws  InvalidArgumentException if the URL can not be parsed
     *
     * @return array
     */
    protected static function parseUrl($url)
    {
        $components = @parse_url($url);
        if (! empty($components)) {
            return $components;
        }

        if (is_null(static::$is_parse_url_bugged)) {
            static::$is_parse_url_bugged = ! is_array(@parse_url("//example.org:80"));
        }

        //bugfix for https://bugs.php.net/bug.php?id=68917
        if (static::$is_parse_url_bugged &&
            strpos($url, '/') === 0 &&
            is_array($components = @parse_url('http:'.$url))
        ) {
            unset($components['scheme']);
            return $components;
        }
        throw new RuntimeException(sprintf("The given URL: `%s` could not be parse", $url));
    }

    protected static function sanitizeUrl($url)
    {
        if ('' == $url || strpos($url, '//') === 0) {
            return $url;
        }

        if (! preg_match(',^((http|ftp|ws)s?:),i', $url, $matches)) {
            return '//'.$url;
        }

        $scheme_length = strlen($matches[0]);
        if (strpos(substr($url, $scheme_length), '//') === 0) {
            return $url;
        }

        return null;
    }

    /**
     * Return a instance of Url from a server array
     *
     * @param array $server the server array
     *
     * @return static
     *
     * @throws RuntimeException If the URL can not be parse
     */
    public static function createFromServer(array $server)
    {
        $scheme = self::fetchServerScheme($server);
        $host = self::fetchServerHost($server);
        $port = self::fetchServerPort($server);
        $request = self::fetchServerRequestUri($server);

        return self::createFromUrl($scheme.$host.$port.$request);
    }

    /**
     * Return the Server URL scheme component
     *
     * @param array $server the server array
     *
     * @return string
     */
    protected static function fetchServerScheme(array $server)
    {
        $scheme = '';
        if (isset($server['SERVER_PROTOCOL'])) {
            $scheme = explode('/', $server['SERVER_PROTOCOL']);
            $scheme = strtolower($scheme[0]);
            if (isset($server['HTTPS']) && 'off' != $server['HTTPS']) {
                $scheme .= 's';
            }
            $scheme .= ':';
        }

        return $scheme.'//';
    }

    /**
     * Return the Server URL host component
     *
     * @param array $server the server array
     *
     * @return string
     *
     * @throws \RuntimeException If no host is detected
     */
    protected static function fetchServerHost(array $server)
    {
        if (isset($server['HTTP_HOST'])) {
            $header = $server['HTTP_HOST'];
            if (! preg_match('/(:\d+)$/', $header, $matches)) {
                return $header;
            }

            return substr($header, 0, -strlen($matches[1]));
        }

        if (isset($server['SERVER_ADDR'])) {
            return $server['SERVER_ADDR'];
        }

        throw new RuntimeException('Host could not be detected');
    }

    /**
     * Return the Server URL port component
     *
     * @param array $server the server array
     *
     * @return string
     */
    protected static function fetchServerPort(array $server)
    {
        $port = '';
        if (isset($server['SERVER_PORT']) && '80' != $server['SERVER_PORT']) {
            $port = ':'. (int) $server['SERVER_PORT'];
        }

        return $port;
    }

    /**
     * Return the Server URL Request Uri component
     *
     * @param array $server the server array
     *
     * @return string
     */
    protected static function fetchServerRequestUri(array $server)
    {
        if (isset($server['REQUEST_URI'])) {
            return $server['REQUEST_URI'];
        }

        if (isset($server['PHP_SELF'])) {
            return $server['PHP_SELF'];
        }

        return '/';
    }

    /**
     * Reformat the component according to the auth content
     *
     * @param array $components the result from parse_url
     *
     * @return array
     */
    protected static function formatAuthComponent(array $components)
    {
        if (!is_null($components['scheme'])
            && is_null($components['host'])
            && !empty($components['path'])
            && strpos($components['path'], '@') !== false
        ) {
            $tmp = explode('@', $components['path'], 2);
            $components['user'] = $components['scheme'];
            $components['pass'] = $tmp[0];
            $components['path'] = $tmp[1];
            $components['scheme'] = null;
        }

        return $components;
    }

    /**
     * Reformat the component according to the host content
     *
     * @param array $components the result from parse_url
     *
     * @return array
     */
    protected static function formatHostComponent(array $components)
    {
        if (strpos($components['host'], '@')) {
            list($auth, $components['host']) = explode('@', $components['host']);
            $components['user'] = $auth;
            $components['pass'] = null;
            if (false !== strpos($auth, ':')) {
                list($components['user'], $components['pass']) = explode(':', $auth);
            }
        }

        return $components;
    }

    /**
     * Reformat the component according to the path content
     *
     * @param array  $components the result from parse_url
     * @param string $url        the original URL to be parse
     *
     * @return array
     */
    protected static function formatPathComponent(array $components, $url)
    {
        if (is_null($components['scheme'])
            && is_null($components['host'])
            && !empty($components['path'])
        ) {
            if (0 === strpos($components['path'], '///')) {
                //even with the added scheme the URL is still broken
                throw new RuntimeException(sprintf('The given URL: `%s` could not be parse', $url));
            }

            if (0 === strpos($components['path'], '//')) {
                $tmp = substr($components['path'], 2);
                $components['path'] = null;
                $res = explode('/', $tmp, 2);
                $components['host'] = $res[0];
                if (isset($res[1])) {
                    $components['path'] = $res[1];
                }
                $components = self::formatHostComponent($components);
            }
        }

        return $components;
    }
}
