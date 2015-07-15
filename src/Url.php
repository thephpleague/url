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

/**
 * A class to manipulate URLs
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Url extends AbstractUrl
{
    /**
     * The Constructor
     * @param Components\Scheme         $scheme   The URL Scheme component
     * @param Components\User           $user     The URL User component
     * @param Components\Pass           $pass     The URL Pass component
     * @param Components\HostInterface  $host     The URL Host component
     * @param Components\Port           $port     The URL Port component
     * @param Components\PathInterface  $path     The URL Path component
     * @param Components\QueryInterface $query    The URL Query component
     * @param Components\Fragment       $fragment The URL Fragment component
     */
    protected function __construct(
        Components\Scheme $scheme,
        Components\User $user,
        Components\Pass $pass,
        Components\HostInterface $host,
        Components\Port $port,
        Components\PathInterface $path,
        Components\QueryInterface $query,
        Components\Fragment $fragment
    ) {
        $this->scheme   = $scheme;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->host     = $host;
        $this->port     = $port;
        $this->path     = $path;
        $this->query    = $query;
        $this->fragment = $fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme($data)
    {
        $this->scheme->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser($data)
    {
        $this->user->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setPass($data)
    {
        $this->pass->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($data)
    {
        $this->host->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setPort($data)
    {
        $this->port->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($data)
    {
        $this->path->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery($data)
    {
        $this->query->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function setFragment($data)
    {
        $this->fragment->set($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }
}
