<?php

namespace League\Url\Test\Components;

use ArrayIterator;
use League\Url\Components\Host;
use PHPUnit_Framework_TestCase;

/**
 * @group components
 */
class HostTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException RuntimeException
     */
    public function testArrayAccess()
    {
        $host = new Host;
        $host[] = 'leheros';
        $this->assertNull($host[5]);
        $this->assertSame('leheros', $host[0]);
        $this->assertSame('leheros', (string) $host);
        $host[0] = 'levilain';
        $host[1] = 'bar';
        $this->assertTrue(isset($host[1]));
        $this->assertCount(2, $host);
        $this->assertSame('levilain.bar', (string) $host);
        foreach ($host as $offset => $value) {
            $this->assertSame($value, $host[$offset]);
        }
        unset($host[0]);
        $this->assertNull($host[0]);
        $this->assertSame(array(1 => 'bar'), $host->toArray());
        $host['toto'] = 'comment ça va';
    }

    public function testIpv4()
    {
        $host = new Host('127.0.0.1');
        $this->assertTrue($host->isIp());
        $this->assertTrue($host->isIpv4());
        $this->assertFalse($host->isIpv6());
        $this->assertSame(array(0 => '127.0.0.1'), $host->toArray());
        $this->assertSame('127.0.0.1', (string) $host);
        $this->assertSame('127.0.0.1', $host->getUriComponent());
    }

    public function testIpv6()
    {
        $expected = 'FE80:0000:0000:0000:0202:B3FF:FE1E:8329';
        $host = new Host($expected);
        $this->assertTrue($host->isIp());
        $this->assertFalse($host->isIpv4());
        $this->assertTrue($host->isIpv6());
        $this->assertSame(array(0 => $expected), $host->toArray());
        $this->assertSame($expected, (string) $host);
        $this->assertSame('['.$expected.']', $host->getUriComponent());
        $this->assertTrue($host->sameValueAs(new Host('['.$expected.']')));
    }

    /**
     * @expectedException LogicException
     */
    public function testAppendWithIpFailed()
    {
        $host = new Host('127.0.0.1');
        $host->append('foo');
    }

    /**
     * @expectedException LogicException
     */
    public function testPrependWithIpFailed()
    {
        $host = new Host('127.0.0.1');
        $host->prepend('foo');
    }

    /**
     * @expectedException LogicException
     */
    public function testRemoveWithIpFailed()
    {
        $host = new Host('127.0.0.1');
        $host->remove('foo');
    }

    public function testPrepend()
    {
        $host = new Host('secure.example.com');

        $host->prepend('master');
        $this->assertSame('master.secure.example.com', $host->get());
    }

    public function testRemove()
    {
        $host = new Host('secure.example.com');
        $host->remove('secure');
        $this->assertSame('example.com', $host->get());
    }

    public function testAppend()
    {
        $host = new Host('secure.example.com');
        $host->append('shop', 'secure');
        $this->assertSame('secure.shop.example.com', $host->get());
    }

    public function testAppendWhence()
    {
        $host = new Host('master.example.com');
        $host->append('master', 'master');
        $host->append('other', 'master', 1);
        $this->assertSame('master.master.other.example.com', $host->get());
    }

    public function testSetterWithString()
    {
        $host = new Host('master.example.com');
        $host->set('.shop.fremium.com');
        $this->assertSame('shop.fremium.com', $host->get());
    }

    public function testSetterWithArray()
    {
        $host = new Host('master.example.com');
        $host->set(array('shop', 'premium', 'org'));
        $this->assertSame('shop.premium.org', $host->get());
    }

    public function testSetterWithArrayIterator()
    {
        $host = new Host('master.example.com');
        $host->set(new ArrayIterator(array('shop', 'premium', 'com')));
        $this->assertSame('shop.premium.com', $host->get());
    }

    public function testPrependWhence()
    {
        $host = new Host('master.example.com');
        $host->prepend('shop');
        $host->prepend('other', 'shop', 1);
        $this->assertSame('other.shop.master.example.com', $host->get());
    }

    public function testSetterWithNull()
    {
        $host = new Host('master.example.com');
        $host->set(null);
        $this->assertNull($host->get());
    }

    /**
     * Test Punycode support
     *
     * @param string $idna_unicode Unicode Hostname
     * @param string $idna_ascii   Ascii Hostname
     * @dataProvider hostnamesProvider
     */
    public function testPunycode($idna_unicode, $idna_ascii)
    {
        $host = new Host($idna_unicode);
        $this->assertSame(explode('.', $idna_unicode), $host->toArray());
        $this->assertSame($idna_ascii, $host->toAscii());
        $this->assertSame($idna_unicode, $host->toUnicode());
        $this->assertSame($idna_unicode, (string) $host);
    }

    public function hostnamesProvider()
    {
        return array(
            array(
                'مثال.إختبار',
                'xn--mgbh0fb.xn--kgbechtv',
            ),
            array(
                '스타벅스코리아.com',
                'xn--oy2b35ckwhba574atvuzkc.com'
            ),
            array(
                'президент.рф',
                'xn--d1abbgf6aiiy.xn--p1ai'
            ),
        );
    }

    /**
     * @expectedException RuntimeException
     */
    public function testHostStatus()
    {
        $host = new Host;
        $host[] = 're view';
    }

    /**
     * @expectedException RuntimeException
     */
    public function testBadHostCharacters()
    {
        new Host('_bad.host.com');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testBadHostLength()
    {
        $host = new Host('secure.example.com');
        $host->append(implode('', array_fill(0, 23, 'banana')), 'secure');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testTooManyHostlabel()
    {
        new Host(array_fill(0, 128, 'a'));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testHosttooLong()
    {
        new Host(array_fill(0, 23, 'banana-slip'));
    }

    public function testSameValueAs()
    {
        $local = new Host('bar.foo');
        $component = new Host('foo.bar');
        $this->assertFalse($local->sameValueAs($component));
        $local->set($component);
        $this->assertTrue($local->sameValueAs($component));
    }
}
