<?php
/*
 * This file is part of the codeliner/php-service-bus.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 11.03.14 - 21:16
 */

namespace Codeliner\ServiceBusTest\Event;

use Codeliner\ServiceBusTest\Mock\PayloadMockObject;
use Codeliner\ServiceBusTest\Mock\SomethingDone;
use Codeliner\ServiceBusTest\TestCase;
use Rhumsaa\Uuid\Uuid;

/**
 * Class EventTest
 *
 * @package Codeliner\ServiceBusTest\Event
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class EventTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_a_payload_array()
    {
        $event = new SomethingDone(array('data' => 'test payload'));

        $this->assertEquals(array('data' => 'test payload'), $event->payload());

        $payloadObject = new PayloadMockObject(array('data' => 'test payload'));

        $event = new SomethingDone($payloadObject);

        $this->assertEquals(array('data' => 'test payload'), $event->payload());
    }

    /**
     * @test
     */
    public function it_has_a_version()
    {
        $event = new SomethingDone(array('data' => 'test payload'));

        $this->assertEquals(1, $event->version());

        $event = new SomethingDone(array('data' => 'test payload'), 2);

        $this->assertEquals(2, $event->version());
    }

    /**
     * @test
     */
    public function it_has_a_uuid()
    {
        $event = new SomethingDone(array('data' => 'test payload'));

        $this->assertInstanceOf('Rhumsaa\Uuid\Uuid', $event->uuid());

        $uuid = Uuid::uuid4();

        $event = new SomethingDone(array('data' => 'test payload'), 2, $uuid);

        $this->assertEquals($uuid->toString(), $event->uuid()->toString());
    }

    /**
     * @test
     */
    public function it_has_a_created_on_datetime()
    {
        $event = new SomethingDone(array('data' => 'test payload'));

        $this->assertInstanceOf('\DateTime', $event->createdOn());

        $createdOn = new \DateTime('2014-03-14 21:27:00');

        $event = new SomethingDone(array('data' => 'test payload'), 1, null, $createdOn);

        $this->assertEquals($createdOn->getTimestamp(), $event->createdOn()->getTimestamp());
    }
}
 