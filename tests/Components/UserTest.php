<?php

namespace League\Url\Test\Components;

use League\Url\Components\User;
use PHPUnit_Framework_TestCase;

/**
 * @group components
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    public function testSameValueAs()
    {
        $component = new User('foo');
        $local = new User;
        $this->assertFalse($local->sameValueAs($component));
        $local->set($component);
        $this->assertTrue($local->sameValueAs($component));
    }

    public function testGetter()
    {
        $local = new User;
        $this->assertNull($local->get());
        $local->set('foobar');
        $this->assertSame('foobar', $local->get());
    }
}
