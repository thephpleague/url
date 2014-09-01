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

/**
 *  A class to manipulate URL Pass component
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Pass implements Component
{
    /**
     * The component data
     *
     * @var string|null
     */
    protected $data;

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
        if (is_null($data)) {
            $this->data = $data;

            return;
        }
        $data = filter_var((string) $data, FILTER_UNSAFE_RAW, array('flags' => FILTER_FLAG_STRIP_LOW));
        if (':' == $data[0]) {
            $data = substr($data, 1);
        }
        $this->data = trim($data);
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
        $value = str_replace(null, '', $this->data);
        if (! empty($value)) {
            $value = ':'.$value;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        return $this->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Pass $component)
    {
        return $this->__toString() === $component->__toString();
    }
}
