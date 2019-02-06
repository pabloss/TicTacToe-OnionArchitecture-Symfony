<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /**
     * @test
     * @expectedException \TypeError
     */
    public function name()
    {
        $event = new Event("test");
        self::assertEquals("test", $event->getName());
        new Event(null);
    }


    /**
     * @test
     * @expectedException \TypeError
     */
    public function construct()
    {
        new Event("test", \json_decode("{}"));
    }
}
