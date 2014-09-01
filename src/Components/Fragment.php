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
 *  A class to manipulate URL Fragment component
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Fragment extends AbstractComponent implements Component
{
    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Fragment $component)
    {
        return $this->__toString() === $component->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $value = $this->__toString();
        if ('' != $value) {
            $value = '#'.$value;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return rawurlencode(str_replace(null, '', $this->data));
    }
}
