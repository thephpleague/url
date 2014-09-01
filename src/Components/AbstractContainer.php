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
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 *  A class to manipulate URL Array like components
 *
 *  @package League.url
 *  @since  3.0.0
 */
abstract class AbstractContainer implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * container holder
     *
     * @var array
     */
    protected $data = array();

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        $args = func_get_args();
        if (! $args) {
            return array_keys($this->data);
        }

        return array_keys($this->data, $args[0], true);
    }

    /**
     * IteratorAggregate Interface method
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Countable Interface method
     *
     * @return integer
     */
    public function count($mode = COUNT_NORMAL)
    {
        return count($this->data, $mode);
    }

    /**
     * ArrayAccess Interface method
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * ArrayAccess Interface method
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * ArrayAccess Interface method
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        return null;
    }

    abstract public function offsetSet($offset, $value);

    public static function isStringable($data)
    {
        return is_string($data) || (is_object($data)) && (method_exists($data, '__toString'));
    }
}
