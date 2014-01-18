<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Brickoo\Tests\Messaging;

use Brickoo\Messaging\Message,
    Brickoo\Messaging\MessageDispatcher,
    PHPUnit_Framework_TestCase;

/**
 * MessageDispatcherTest
 *
 * Test suite for the MessageDispatcher class.
 * @see Brickoo\Messaging\MessageDispatcher
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageDispatcherTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Messaging\MessageDispatcher::__construct
     * @covers Brickoo\Messaging\MessageDispatcher::attach
     */
    public function testAttachListener() {
        $listenerUID = uniqid();
        $listener = $this->getListenerStub();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                          ->method("add")
                          ->with($listener)
                          ->will($this->returnValue($listenerUID));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );
        $this->assertEquals($listenerUID, $messageDispatcher->attach($listener));
    }

    /** @covers Brickoo\Messaging\MessageDispatcher::attachAggregatedListeners */
    public function testAttachAggregatedListeners() {
        require_once "Assets/AggregatedListeners.php";

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("add")
                           ->with($this->isInstanceOf("\\Brickoo\\Messaging\\Listener"))
                           ->will($this->returnValue(uniqid()));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );

        $listener = new Assets\AggregatedListeners();
        $this->assertEquals($messageDispatcher, $messageDispatcher->attachAggregatedListeners($listener));
    }

    /** @covers Brickoo\Messaging\MessageDispatcher::detach */
    public function testDetachListener() {
        $listenerUID = uniqid();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("remove")
                           ->with($listenerUID)
                           ->will($this->returnValue(true));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );
        $this->assertSame($messageDispatcher, $messageDispatcher->detach($listenerUID));
    }

    /**
     * @covers Brickoo\Messaging\MessageDispatcher::detach
     * @expectedException InvalidArgumentException
     */
    public function testDetachListenerIdentifierThrowsArgumentException() {
        $messageDispatcher = new MessageDispatcher(
            $this->getListenerCollectionStub(),
            $this->getMessageRecursionDepthListStub()
        );
        $messageDispatcher->detach(["wrongType"]);
    }

    /**
     * @covers Brickoo\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Messaging\MessageDispatcher::processMessage
     */
    public function testDispatch() {
        $messageName = "test.message.manager.notify";

        $message = $this->getMessageStub();
        $message->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($messageName));

        $messageDispatcher = $this->getMessageDispatcherFixture($messageName, $message);
        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /** @covers Brickoo\Messaging\MessageDispatcher::dispatch */
    public function testDispatchWithoutListeners() {
        $messageName = "test.message.manager.notify";

        $message = $this->getMessageStub();
        $message->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($messageName));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("hasListeners")
                           ->with($messageName)
                           ->will($this->returnValue(false));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );
        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /**
     * @covers Brickoo\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Messaging\MessageDispatcher::processMessage
     */
    public function testDispatchWithRespondingListeners() {
        $messageName = "test.message.manager.notify";

        $message = $this->getMessageStub();
        $message->expects($this->any())
                ->method("getName")
                ->will($this->returnValue($messageName));
        $message->expects($this->any())
                ->method("isStopped")
                ->will($this->returnValue(true));
        $message->expects($this->any())
                ->method("setResponse")
                ->with($this->isInstanceOf("\\Brickoo\\Messaging\\MessageResponseCollection"))
                ->will($this->returnValue(true));

        $listenerCollection = $this->getListenerCollectionStub();

        $messageRecursionDepthList = $this->getMessageRecursionDepthListStub();
        $messageRecursionDepthList->expects($this->once())
                                  ->method("isDepthLimitReached")
                                  ->with($messageName)
                                  ->will($this->returnValue(false));

        $messageDispatcher = new MessageDispatcher($listenerCollection, $messageRecursionDepthList);

        $listener = $this->getListenerStub();
        $listener->expects($this->any())
                 ->method("handleMessage")
                 ->with($message, $messageDispatcher)
                 ->will($this->returnValue("response"));

        $listenerCollection->expects($this->once())
                           ->method("hasListeners")
                           ->with($messageName)
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->once())
                           ->method("getListeners")
                           ->with($messageName)
                           ->will($this->returnValue([$listener]));

        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /**
     * @covers Brickoo\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Messaging\MessageDispatcher::processMessage
     * @covers Brickoo\Messaging\Exception\MaxRecursionDepthReachedException
     * @expectedException Brickoo\Messaging\Exception\MaxRecursionDepthReachedException
     */
    public function testProcessRecursionDepthLimitIsDetected() {
        $messageName = "test.message.manager.infinite.loop";

        $message = $this->getMessageStub();
        $message->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($messageName));

        $messageRecursionDepthList = $this->getMessageRecursionDepthListStub();
        $messageRecursionDepthList->expects($this->once())
                                ->method("isDepthLimitReached")
                                ->with($messageName)
                                ->will($this->returnValue(true));
        $messageRecursionDepthList->expects($this->once())
                                ->method("getRecursionDepth")
                                ->with($messageName)
                                ->will($this->returnValue(10));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $messageRecursionDepthList
        );
        $messageDispatcher->dispatch($message);
    }

    /**
     * Returns an message dispatcher fixture configured with the arguments.
     * @param string $messageName the message name
     * @param \Brickoo\Messaging\Message $message
     * @param string|null $expectedResult the expected processor result
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherFixture($messageName, Message $message, $expectedResult = null) {
        $expectedResult = empty($expectedResult) ? [] : [$expectedResult];
        $listener = $this->getListenerStub();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->any())
                           ->method("getListeners")
                           ->will($this->returnValue([$listener]));

        $messageRecursionDepthList = $this->getMessageRecursionDepthListStub();
        $messageRecursionDepthList->expects($this->once())
                  ->method("isDepthLimitReached")
                  ->with($messageName)
                  ->will($this->returnValue(false));
        $messageRecursionDepthList->expects($this->once())
                  ->method("increaseDepth")
                  ->with($messageName)
                  ->will($this->returnSelf());
        $messageRecursionDepthList->expects($this->once())
                  ->method("decreaseDepth")
                  ->with($messageName)
                  ->will($this->returnSelf());

        return new MessageDispatcher($listenerCollection, $messageRecursionDepthList);
    }

    /**
     * Returns a listener collection stub.
     * @return \Brickoo\Messaging\ListenerCollection
     */
    private function getListenerCollectionStub() {
        return $this->getMockBuilder("\\Brickoo\\Messaging\\ListenerCollection")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message listener stub.
     * @return \Brickoo\Messaging\Listener
     */
    private function getListenerStub() {
        return $this->getMock("\\Brickoo\\Messaging\\Listener");
    }

    /**
     * Returns a message recursion depth list stub.
     * @return \Brickoo\Messaging\MessageRecursionDepthList
     */
    private function getMessageRecursionDepthListStub() {
        return $this->getMockBuilder("\\Brickoo\\Messaging\\MessageRecursionDepthList")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message stub.
     * @return \Brickoo\Messaging\Message
     */
        private function getMessageStub() {
        return $this->getMock("\\Brickoo\\Messaging\\Message");
    }

}