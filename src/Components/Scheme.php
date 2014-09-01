<?php
/**
* This file is part of the League.url library
*
* @license http://opensource.org/licenses/MIT
* @link https://github.com/thephpleague/url/
* @version 3.2.0
* @package League.url
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace League\Url\Components;

use RuntimeException;

/**
 *  A class to manipulate URL Scheme component
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Scheme implements Component
{
    /**
     * The component data
     *
     * @var string|null
     */
    protected $data;

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $value = $this->__toString();
        if ('' != $value) {
            $value .= '://';
        }

        return $value;
    }

    /**
     * The Constructor
     *
     * @param mixed $data the component data
     */
    public function __construct($data = null)
    {
        $this->set($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($data)
    {
        $data = $this->sanitizeComponent($data);
        if (is_null($data)) {
            $this->data = null;

            return;
        }

        $data = filter_var($data, FILTER_VALIDATE_REGEXP, array(
            'options' => array('regexp' => '/^[a-z][a-z0-9+-.]+$/i')
        ));

        if (! $data) {
            throw new RuntimeException('This class only deals with http URL');
        }

        $this->data = strtolower($data);
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return str_replace(null, '', $this->data);
    }

    /**
     * Sanitize a string component
     *
     * @param mixed $str
     *
     * @return string|null
     */
    protected function sanitizeComponent($str)
    {
        if (is_null($str)) {
            return $str;
        }
        $str = filter_var((string) $str, FILTER_UNSAFE_RAW, array('flags' => FILTER_FLAG_STRIP_LOW));
        $str = trim($str);

        return $str;
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Component $component)
    {
        return $this->__toString() === $component->__toString();
    }
}
