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

use LogicException;
use RuntimeException;
use True\Punycode;

/**
 *  A class to manipulate URL Host component
 *
 *  @package League.url
 *  @since  1.0.0
 */
class Host extends AbstractSegment implements Component
{
    /**
     * {@inheritdoc}
     */
    protected $delimiter = '.';

    protected $host_as_ipv6 = false;

    protected $host_as_ipv4 = false;
    /**
     * Punycode Alogrithm Object
     * @var \True\Punycode
     */
    protected $punycode;

    /**
     * Environment Internal encoding
     * @var mixed
     */
    protected $encoding;

    /**
     * Alter the Environment Internal Encoding if it is not utf-8
     *
     * @return void
     */
    protected function saveInternalEncoding()
    {
        $this->encoding = mb_internal_encoding();
        if (stripos($this->encoding, 'utf-8') === false) {
            mb_internal_encoding('utf-8');
        }
    }

    /**
     * Restore the Environment Internal Encoding
     *
     * @return void
     */
    protected function restoreInternalEncoding()
    {
        mb_internal_encoding($this->encoding);
    }

    /**
     * {@inheritdoc}
     */
    public function __construct($data = null)
    {
        $this->punycode = new Punycode;
        parent::__construct($data);
    }

    public function isIpv4()
    {
        return $this->host_as_ipv4;
    }

    public function isIpv6()
    {
        return $this->host_as_ipv6;
    }

    public function isIp()
    {
        return $this->host_as_ipv6 || $this->host_as_ipv4;
    }

    protected function setHostAsIp($str)
    {
        $this->host_as_ipv4 = false;
        $this->host_as_ipv6 = false;
        if (! self::isStringable($str)) {
            return false;
        }

        $str = (string) $str;
        $str = trim($str);
        if ('[' == $str[0] && ']' == $str[strlen($str)-1]) {
            $str = substr($str, 1, -1);
        }

        if (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->host_as_ipv4 = true;
            $this->host_as_ipv6 = false;
            $this->data = array($str);

            return true;
        } elseif (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->host_as_ipv4 = false;
            $this->host_as_ipv6 = true;
            $this->data = array($str);

            return true;
        }

        return false;
    }

    protected function assertHostAsIp()
    {
        if ($this->isIp()) {
            throw new LogicException('You can not modify a IP based host');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($data)
    {

        if ($this->setHostAsIp($data)) {
            return;
        }

        $this->data = array_filter($this->validate($data), function ($value) {
            return ! is_null($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $res = array();
        foreach (array_values($this->data) as $value) {
            $res[] = $this->punycode->decode($value);
        }
        if (! $res) {
            return null;
        }

        return implode($this->delimiter, $res);
    }

    /**
     * {@inheritdoc}
     */
    public function toAscii()
    {
        $this->saveInternalEncoding();
        $res = $this->punycode->encode($this->__toString());
        $this->restoreInternalEncoding();

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function toUnicode()
    {
        return $this->__toString();
    }

    /**
     * Validate Host data before insertion into a URL host component
     *
     * @param mixed $data the data to insert
     *
     * @return array
     *
     * @throws RuntimeException If the added is invalid
     */
    protected function validate($data)
    {
        $data = $this->validateSegment($data);
        if (! $data) {
            return $data;
        }

        //the 63 length must be checked before unicode application
        $this->saveInternalEncoding();
        $res = array_filter($data, function ($label) {
            return mb_strlen($label) > 63;
        });
        if (count($res)) {
            $this->restoreInternalEncoding();
            throw new RuntimeException('Invalid hostname, check its length');
        }

        $data = explode(
            $this->delimiter,
            $this->punycode->encode(implode($this->delimiter, $data))
        );

        $res = preg_grep('/^[0-9a-z]([0-9a-z-]{0,61}[0-9a-z])?$/i', $data, PREG_GREP_INVERT);
        if (count($res)) {
            $this->restoreInternalEncoding();
            throw new RuntimeException('Invalid host label, check its content');
        }

        $host = $this->data;
        $imploded = implode($this->delimiter, $data);
        $nb_labels = count($host) + count($data);
        if (count($data) && (2 > $nb_labels || 127 <= $nb_labels)) {
            throw new RuntimeException('Host may have between 2 and 127 parts');
        } elseif (225 <= (strlen(implode($this->delimiter, $host)) + strlen($imploded) + 1)) {
            throw new RuntimeException('Host may have a maximum of 255 characters');
        }

        $data = explode(
            $this->delimiter,
            $this->punycode->decode(implode(
                $this->delimiter,
                $this->sanitizeValue($data)
            ))
        );
        $this->restoreInternalEncoding();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function append($data, $whence = null, $whence_index = null)
    {
        $this->assertHostAsIp();

        return parent::append($data, $whence, $whence_index);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend($data, $whence = null, $whence_index = null)
    {
        $this->assertHostAsIp();

        return parent::prepend($data, $whence, $whence_index);
    }

    public function offsetSet($offset, $value)
    {
        $this->assertHostAsIp();

        return parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data)
    {
        $this->assertHostAsIp();

        return parent::remove($data);
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Host $component)
    {
        return $this->__toString() === $component->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $str = $this->__toString();
        if ($this->host_as_ipv6) {
            return '['.$str.']';
        }

        return $str;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->get();
    }
}
