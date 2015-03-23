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

use ArrayAccess;
use RuntimeException;

/**
 *  A class to manipulate URL Query component
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Query extends AbstractArray implements QueryInterface, ArrayAccess
{
    /**
     * The Constructor
     *
     * @param mixed $data can be string, array or Traversable
     *                    object convertible into Query String
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
        $this->data = array_filter($this->validate($data), function ($value) {
            if (is_string($value)) {
                $value = trim($value);
            }

            return null !== $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!$this->data) {
            return null;
        }

        return str_replace(
            array('%E7', '+'),
            array('~', '%20'),
            http_build_query($this->data, '', '&')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->get();
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(ComponentInterface $component)
    {
        return $this->__toString() == $component->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $value = $this->__toString();
        if ('' != $value) {
            $value = '?'.$value;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($data)
    {
        $this->set(array_merge($this->data, $this->validate($data)));
    }

    /**
     * {@inheritdoc}
     */
    protected function validate($data)
    {
        return $this->convertToArray($data, function ($str) {
            if ('' == $str) {
                return array();
            }
            if ('?' == $str[0]) {
                $str = substr($str, 1);
            }

            // alternative to parse_str to fix #31 and #58
            $parts = explode('&', $str);
            $arr = array();
            foreach ($parts as $part) {
                $part = explode('=', $part, 2);
                $arr[$part[0]] = isset($part[1]) ? $part[1] : '';
            }
            $arr = $this->parseArrayParams($arr);

            return $arr;
        });
    }

    protected function parseArrayParams($inputArray) {
        $result = array();
        foreach ($inputArray as $key => $val) {
            $keyParts = preg_split('/[\[\]]+/', $key, -1, PREG_SPLIT_NO_EMPTY);
            $ref = &$result;
            while ($keyParts) {
                $part = array_shift($keyParts);
                if (!isset($ref[$part])) {
                    $ref[$part] = array();
                }
                $ref = &$ref[$part];
            }
            $ref = $val;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new RuntimeException('offset can not be null');
        }
        $this->modify(array($offset => $value));
    }
}
