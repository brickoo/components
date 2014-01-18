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

namespace Brickoo\Tests\Http;

use Brickoo\Http\ResponseSender,
    PHPUnit_Framework_TestCase;

/**
 * ResponseSenderTest
 *
 * Test suite for the ResponseSender class.
 * @see Brickoo\Http\ResponseSender
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ResponseSenderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\ResponseSender::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidFunctionThrowsException() {
        new ResponseSender("functionNotDefined");
    }

    /**
     * @covers Brickoo\Http\ResponseSender::__construct
     * @covers Brickoo\Http\ResponseSender::send
     * @covers Brickoo\Http\ResponseSender::sendStatus
     * @covers Brickoo\Http\ResponseSender::sendMessageHeader
     * @covers Brickoo\Http\ResponseSender::sendMessageBody
     * @covers Brickoo\Http\ResponseSender::checkStatusAllowsMessageBodyContent
     */
    public function testSendResponse() {
        $headerFunction = function(){};

        $httpVersion = $this->getMockBuilder("\\Brickoo\\Http\\HttpVersion")
            ->disableOriginalConstructor()->getMock();
        $httpVersion->expects($this->any())
                    ->method("toString")
                    ->will($this->returnValue("HTTP/1.1"));

        $messageHeader = $this->getMockBuilder("\\Brickoo\\Http\\MessageHeader")
            ->disableOriginalConstructor()->getMock();
        $messageHeader->expects($this->any())
                      ->method("toArray")
                      ->will($this->returnValue(array("Location", "example.com")));
        $messageBody = $this->getMockBuilder("\\Brickoo\\Http\\MessageBody")
            ->disableOriginalConstructor()->getMock();
        $messageBody->expects($this->any())
                    ->method("getContent")
                    ->will($this->returnValue("response content"));

        $httpResponse = $this->getMockBuilder("\\Brickoo\\Http\\HttpResponse")
            ->disableOriginalConstructor()->getMock();
        $httpResponse->expects($this->any())
                     ->method("getStatus")
                     ->will($this->returnValue(200));
        $httpResponse->expects($this->any())
                     ->method("getStatusPhrase")
                     ->will($this->returnValue("OK"));
        $httpResponse->expects($this->any())
                     ->method("getVersion")
                     ->will($this->returnValue($httpVersion));
        $httpResponse->expects($this->any())
                     ->method("getHeader")
                     ->will($this->returnValue($messageHeader));
        $httpResponse->expects($this->any())
                     ->method("getBody")
                     ->will($this->returnValue($messageBody));

        $responseSender = new ResponseSender($headerFunction);
        $this->assertNull($responseSender->send($httpResponse));
        $this->expectOutputString("response content");
    }

    /**
     * @covers Brickoo\Http\ResponseSender::send
     * @covers Brickoo\Http\ResponseSender::checkStatusAllowsMessageBodyContent
     * @covers Brickoo\Http\Exception\StatusCodeDoesNotAllowMessageBodyException
     * @expectedException  Brickoo\Http\Exception\StatusCodeDoesNotAllowMessageBodyException
     */
    public function testStatusDoesCOntentNotAllowThrowsException() {
        $messageBody = $this->getMockBuilder("\\Brickoo\\Http\\MessageBody")
            ->disableOriginalConstructor()->getMock();
        $messageBody->expects($this->any())
                    ->method("getContent")
                    ->will($this->returnValue("not allowed content"));

        $httpResponse = $this->getMockBuilder("\\Brickoo\\Http\\HttpResponse")
            ->disableOriginalConstructor()->getMock();
        $httpResponse->expects($this->any())
                     ->method("getStatus")
                     ->will($this->returnValue(304));
        $httpResponse->expects($this->any())
                     ->method("getBody")
                     ->will($this->returnValue($messageBody));

        $responseSender = new ResponseSender();
        $responseSender->send($httpResponse);
    }

}