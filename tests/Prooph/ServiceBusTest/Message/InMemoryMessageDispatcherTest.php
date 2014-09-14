<?php
/*
 * This file is part of the prooph/php-service-bus.
 * (c) Alexander Miertsch <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 10.03.14 - 21:19
 */

namespace Prooph\ServiceBusTest\Message;

use Prooph\ServiceBus\Message\InMemoryMessageDispatcher;
use Prooph\ServiceBus\Message\MessageHeader;
use Prooph\ServiceBus\Message\Queue;
use Prooph\ServiceBus\Message\StandardMessage;
use Prooph\ServiceBus\Service\Definition;
use Prooph\ServiceBus\Service\ServiceBusConfiguration;
use Prooph\ServiceBus\Service\ServiceBusManager;
use Prooph\ServiceBusTest\Mock\HandleCommandHandler;
use Prooph\ServiceBusTest\TestCase;
use Rhumsaa\Uuid\Uuid;
use Zend\EventManager\EventInterface;

/**
 * Class InMemoryMessageDispatcherTest
 *
 * @package Prooph\ServiceBusTest\Message
 * @author Alexander Miertsch <contact@prooph.de>
 */
class InMemoryMessageDispatcherTest extends TestCase
{
    /**
     * @var InMemoryMessageDispatcher
     */
    private $messageDispatcher;

    /**
     * @var HandleCommandHandler
     */
    private $commandHandler;


    protected function setUp()
    {
    }

    /**
     * @test
     */
    public function it_dispatches_a_message_synchronous()
    {
        $this->markTestSkipped("reactivate after redesign is finished");

        $message = new StandardMessage(
            'Prooph\ServiceBusTest\Mock\DoSomething',
            new MessageHeader(Uuid::uuid4(), new \DateTime(), 1, 'test-case-bus', MessageHeader::TYPE_COMMAND),
            array('data' => 'test payload')
        );

        $localQueue = new Queue('local');

        $this->messageDispatcher->dispatch($localQueue, $message);

        $this->assertEquals('test payload', $this->commandHandler->lastCommand()->data());
    }

    /**
     * @test
     */
    public function it_triggers_all_events()
    {
        $this->markTestSkipped("reactivate after redesign is finished");

        $preDispatchTriggered  = false;
        $postDispatchTriggered = false;

        $message = new StandardMessage(
            'Prooph\ServiceBusTest\Mock\DoSomething',
            new MessageHeader(Uuid::uuid4(), new \DateTime(), 1, 'test-case-bus', MessageHeader::TYPE_COMMAND),
            array('data' => 'test payload')
        );

        $localQueue = new Queue('local');

        $this->messageDispatcher->events()->attach(
            'dispatch.pre',
            function (EventInterface $e) use (&$preDispatchTriggered, $localQueue, $message) {
                $this->assertSame($localQueue, $e->getParam('queue'));
                $this->assertSame($message, $e->getParam('message'));
                $preDispatchTriggered = true;
            }
        );

        $this->messageDispatcher->events()->attach(
            'dispatch.post',
            function (EventInterface $e) use (&$postDispatchTriggered, $localQueue, $message) {
                $this->assertSame($localQueue, $e->getParam('queue'));
                $this->assertSame($message, $e->getParam('message'));
                $this->assertInstanceOf('Prooph\ServiceBus\Command\CommandReceiver', $e->getParam('receiver'));
                $postDispatchTriggered = true;
            }
        );

        $this->messageDispatcher->dispatch($localQueue, $message);

        $this->assertTrue($preDispatchTriggered);
        $this->assertTrue($postDispatchTriggered);
    }
}