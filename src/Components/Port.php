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
 *  A class to manipulate URL Port component
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Port extends AbstractComponent implements Component
{
    /**
     * {@inheritdoc}
     */
    public function set($data)
    {
        if (is_null($data)) {
            $this->data = null;

            return;
        }

        $data = (string) $data;
        $data = trim($data);

        $data = filter_var($data, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));

        if (! $data) {
            throw new RuntimeException('A port must be a valid positif integer');
        }

        $this->data = (int) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Port $component)
    {
        return $this->__toString() === $component->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $value = $this->__toString();
        if (! empty($value)) {
            return ':'.$value;
        }

        return $value;
    }
}
