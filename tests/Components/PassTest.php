<?php

namespace League\Url\Test\Components;

use League\Url\Components\Pass;
use PHPUnit_Framework_TestCase;

/**
 * @group components
 */
class PassTest extends PHPUnit_Framework_TestCase
{
    public function testSameValueAs()
    {
        $local = new Pass('bar');
        $component = new Pass('foo');
        $this->assertFalse($local->sameValueAs($component));
        $local->set($component);
        $this->assertTrue($local->sameValueAs($component));
    }

    public function testGetter()
    {
        $local = new Pass;
        $this->assertNull($local->get());
        $local->set('foobar');
        $this->assertSame('foobar', $local->get());
    }
}
