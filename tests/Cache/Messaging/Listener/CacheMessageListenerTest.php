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

namespace Brickoo\Tests\Component\Cache\Messaging\Listener;

use Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener,
    Brickoo\Component\Cache\Messaging\Message\DeleteMessage,
    Brickoo\Component\Cache\Messaging\Message\FlushMessage,
    Brickoo\Component\Cache\Messaging\Message\RetrieveByCallbackMessage,
    Brickoo\Component\Cache\Messaging\Message\RetrieveMessage,
    Brickoo\Component\Cache\Messaging\Message\StoreMessage,
    PHPUnit_Framework_TestCase;

/**
 * CacheMessageListenerTest
 *
 * Test suite for the CacheMessageListener class.
 * @see Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheMessageListenerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::__construct
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachListeners
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachRetrieveMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachRetrieveByCallbackMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachStoreMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachDeleteMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachFlushMessageListener
     */
    public function testAttachCacheMessageListenersToMessageDispatcher() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->exactly(5))
                          ->method("attach")
                          ->with($this->isInstanceOf("\\Brickoo\\Component\\Messaging\\Listener"));

        $cacheMessageListener = new CacheMessageListener($this->getCacheProxyStub());
        $cacheMessageListener->attachListeners($messageDispatcher);
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveMessage */
    public function testHandleRetrieveMessage() {
        $cacheIdentifier = "test";
        $cachedResponse  = "cached test response";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("get")
                   ->with($cacheIdentifier)
                   ->will($this->returnValue($cachedResponse));

        $message = new RetrieveMessage($cacheIdentifier);
        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheMessageListener->handleRetrieveMessage($message, $this->getMessageDispatcherStub())
         );
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveMessage */
    public function testHandleRetrieveMessageWithInvalidMessage() {
        $message = new FlushMessage();
        $cacheMessageListener = new CacheMessageListener($this->getCacheProxyStub());
        $this->assertNull($cacheMessageListener->handleRetrieveMessage($message, $this->getMessageDispatcherStub()));
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveByCallbackMessage */
    public function testHandleRetrieveByCallbackMessage() {
        $cacheIdentifier = "test";
        $cacheCallback = function(){ /*do nothing */ };
        $callbackArguments = [];
        $lifetime = 60;
        $cachedResponse = "cached test callback response";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("getByCallback")
                   ->with($cacheIdentifier, $cacheCallback, $callbackArguments, $lifetime)
                   ->will($this->returnValue($cachedResponse));

        $message = new RetrieveByCallbackMessage(
            $cacheIdentifier,
            $cacheCallback,
            $callbackArguments,
            $lifetime
        );

        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheMessageListener->handleRetrieveByCallbackMessage($message, $this->getMessageDispatcherStub())
        );
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveByCallbackMessage */
    public function testHandleRetrieveByCallbackMessageWithInvalidMessage() {
        $message = new FlushMessage();
        $cacheMessageListener = new CacheMessageListener($this->getCacheProxyStub());
        $this->assertNull($cacheMessageListener->handleRetrieveByCallbackMessage($message, $this->getMessageDispatcherStub()));
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleStoreMessage */
    public function testHandleStoreMessage() {
        $cacheIdentifier = "test";
        $content = "content to cache";
        $cacheLifetime = 60;

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("set")
                   ->with($cacheIdentifier, $content, $cacheLifetime)
                   ->will($this->returnSelf());

        $message = new StoreMessage(
            $cacheIdentifier,
            $content,
            $cacheLifetime
        );

        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $cacheMessageListener->handleStoreMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleDeleteMessage */
    public function testHandleDeleteMessage() {
        $cacheIdentifier = "test";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("delete")
                   ->with($cacheIdentifier)
                   ->will($this->returnSelf());

        $message = new DeleteMessage($cacheIdentifier);
        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $cacheMessageListener->handleDeleteMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleFlushMessage */
    public function testHandleFlushMessage() {
        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("flush")
                   ->will($this->returnSelf());

        $message = new FlushMessage();
        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $cacheMessageListener->handleFlushMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /**
     * Returns a cache proxy stub.
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    private function getCacheProxyStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Cache\\CacheProxy")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an message dispatcher stub.
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}