<?php

namespace League\Url;

/**
 * Punycode implementation as described in RFC 3492
 *
 * @link http://tools.ietf.org/html/rfc3492
 */
final class Punycode
{
    /**
     * Bootstring parameter values
     *
     */
    const BASE         = 36;
    const TMIN         = 1;
    const TMAX         = 26;
    const SKEW         = 38;
    const DAMP         = 700;
    const INITIAL_BIAS = 72;
    const INITIAL_N    = 128;
    const PREFIX       = 'xn--';
    const DELIMITER    = '-';

    /**
     * Encode table
     *
     * @param array
     */
    protected static $transcodeTable = array(
        'a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3',
        '4', '5', '6', '7', '8', '9',
    );

    /**
     * Encode a string using the punycode algorythm
     *
     * @param string $input string
     *
     * @return string
     */
    public function encode($input)
    {
        $encoding = mb_internal_encoding();
        if (stripos($encoding, 'utf-8') === false) {
            mb_internal_encoding('utf-8');
        }

        $codePoints = $this->getCodePoints($input);

        $n = static::INITIAL_N;
        $bias = static::INITIAL_BIAS;
        $delta = 0;
        $h = $b = count($codePoints['basic']);

        $output = '';
        foreach ($codePoints['basic'] as $code) {
            $output .= $this->codepointToChar($code);
        }
        if ($input === $output) {
             mb_internal_encoding($encoding);

            return $output;
        }
        if ($b > 0) {
            $output .= static::DELIMITER;
        }

        $i = 0;
        $length = mb_strlen($input);
        while ($h < $length) {
            $m = $codePoints['nonBasic'][$i++];
            $delta = $delta + ($m - $n) * ($h + 1);
            $n = $m;

            foreach ($codePoints['all'] as $c) {
                if ($c < $n || $c < static::INITIAL_N) {
                    $delta++;
                }
                if ($c === $n) {
                    $q = $delta;
                    for ($k = static::BASE;; $k += static::BASE) {
                        if ($k <= $bias + static::TMIN) {
                            $t = static::TMIN;
                        } elseif ($k >= $bias + static::TMAX) {
                            $t = static::TMAX;
                        } else {
                            $t = $k - $bias;
                        }
                        if ($q < $t) {
                            break;
                        }

                        $code = $t + (($q - $t) % (static::BASE - $t));
                        $output .= static::$transcodeTable[$code];

                        $q = ($q - $t) / (static::BASE - $t);
                    }

                    $output .= static::$transcodeTable[$q];
                    $bias = $this->adapt($delta, $h + 1, ($h === $b));
                    $delta = 0;
                    $h++;
                }
            }

            $delta++;
            $n++;
        }
        mb_internal_encoding($encoding);

        return static::PREFIX . $output;
    }

    /**
     * Decode a string encoded using the punycode algorythm
     *
     * @param string $input encoded string using punycode
     *
     * @return string
     */
    public function decode($input)
    {
        if (strpos($input, static::PREFIX) !== 0) {
            return $input;
        }
        $input = ltrim($input, static::PREFIX);

        $encoding = mb_internal_encoding();
        if (stripos($encoding, 'utf-8') === false) {
            mb_internal_encoding('utf-8');
        }

        $n = static::INITIAL_N;
        $i = 0;
        $bias = static::INITIAL_BIAS;
        $output = '';

        $pos = strrpos($input, static::DELIMITER);
        if ($pos !== false) {
            $output = substr($input, 0, $pos++);
        } else {
            $pos = 0;
        }

        $outputLength = strlen($output);
        $inputLength = strlen($input);
        $decodeTable = array_flip(static::$transcodeTable);
        while ($pos < $inputLength) {
            $oldi = $i;
            $w = 1;

            for ($k = static::BASE;; $k += static::BASE) {
                $digit = $decodeTable[$input[$pos++]];
                $i = $i + ($digit * $w);

                if ($k <= $bias + static::TMIN) {
                    $t = static::TMIN;
                } elseif ($k >= $bias + static::TMAX) {
                    $t = static::TMAX;
                } else {
                    $t = $k - $bias;
                }
                if ($digit < $t) {
                    break;
                }

                $w = $w * (static::BASE - $t);
            }

            $bias = $this->adapt($i - $oldi, ++$outputLength, ($oldi === 0));
            $n = $n + (int) ($i / $outputLength);
            $i = $i % ($outputLength);
            $output = mb_substr($output, 0, $i)
                .$this->codepointToChar($n)
                .mb_substr($output, $i, $outputLength - 1);

            $i++;
        }

        mb_internal_encoding($encoding);

        return $output;
    }

    /**
     * Bias adaptation function as per section 3.4 of RFC 3492.
     *
     * @param float   $delta
     * @param integer $num_points
     * @param boolean $first_time
     *
     * @return integer
     */
    protected function adapt($delta, $num_points, $first_time)
    {
        $key = 0;
        $delta = $first_time ? floor($delta / static::DAMP) : $delta >> 1;
        $delta += floor($delta / $num_points);

        $tmp = static::BASE - static::TMIN;
        for (; $delta > $tmp * static::TMAX >> 1; $key += static::BASE) {
            $delta = floor($delta / $tmp);
        }

        return floor($key + ($tmp + 1) * $delta / ($delta + static::SKEW));
    }

    /**
     * List code points for a given input
     *
     * @param string $input
     *
     * @return array Multi-dimension array with basic, non-basic and aggregated code points
     */
    protected function getCodePoints($input)
    {
        $codePoints = array(
            'all'      => array(),
            'basic'    => array(),
            'nonBasic' => array(),
        );

        $length = mb_strlen($input);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($input, $i, 1);
            $code = static::charToCodepoint($char);
            $codePoints['all'][] = $code;
            $offset = 'nonBasic';
            if ($code < 128) {
                $offset = 'basic';
            }
            $codePoints[$offset][] = $code;
        }

        $codePoints['nonBasic'] = array_unique($codePoints['nonBasic']);
        sort($codePoints['nonBasic']);

        return $codePoints;
    }

    /**
     * Convert a single or multi-byte character to its codepoint
     *
     * @param string $char
     *
     * @return integer
     */
    protected function charToCodepoint($char)
    {
        $code = ord($char[0]);
        if ($code < 128) {
            return $code;
        } elseif ($code < 224) {
            return (($code - 192) * 64) + (ord($char[1]) - 128);
        } elseif ($code < 240) {
            return (($code - 224) * 4096) + ((ord($char[1]) - 128) * 64) + (ord($char[2]) - 128);
        }

        return (($code - 240) * 262144)
            + ((ord($char[1]) - 128) * 4096)
            + ((ord($char[2]) - 128) * 64)
            + (ord($char[3]) - 128);
    }

    /**
     * Convert a codepoint to its single or multi-byte character
     *
     * @param integer $code
     *
     * @return string
     */
    protected function codepointToChar($code)
    {
        if ($code <= 0x7F) {
            return chr($code);
        } elseif ($code <= 0x7FF) {
            return chr(($code >> 6) + 192) . chr(($code & 63) + 128);
        } elseif ($code <= 0xFFFF) {
            return chr(($code >> 12) + 224) . chr((($code >> 6) & 63) + 128) . chr(($code & 63) + 128);
        }

        return chr(($code >> 18) + 240)
            .chr((($code >> 12) & 63) + 128)
            .chr((($code >> 6) & 63) + 128)
            .chr(($code & 63) + 128);
    }
}
