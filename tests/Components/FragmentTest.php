<?php

namespace League\Url\Test\Components;

use League\Url\Components\Fragment;
use PHPUnit_Framework_TestCase;

/**
 * @group components
 */
class FragmentTest extends PHPUnit_Framework_TestCase
{

    public function testSameValueAs()
    {
        $component = new Fragment('foo');
        $local = new Fragment;
        $this->assertFalse($local->sameValueAs($component));
        $local->set($component);
        $this->assertTrue($local->sameValueAs($component));
    }

    public function testGetter()
    {
        $local = new Fragment;
        $this->assertNull($local->get());
        $local->set('foobar');
        $this->assertSame('foobar', $local->get());
    }
}
