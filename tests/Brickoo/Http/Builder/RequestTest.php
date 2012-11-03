<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Tests\Brickoo\Http\Builder;

    use Brickoo\Http\Builder\Request;

    /**
     * RequestTest
     *
     * Test suite for the Builder\Request class.
     * @see Brickoo\Http\Builder\Request
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Builder\Request::setHeader
         */
        public function testSetHeaderDependency() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $RequestBuilder = new Request();
            $RequestBuilder->setHeader($Header);
            $this->assertAttributeSame($Header, "Header", $RequestBuilder);
        }

        /**
         * @covers Brickoo\Http\Builder\Request::setBody
         */
        public function testSetBodyDependency() {
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $RequestBuilder = new Request();
            $RequestBuilder->setBody($Body);
            $this->assertAttributeSame($Body, "Body", $RequestBuilder);
        }

        /**
         * @covers Brickoo\Http\Builder\Request::setUri
         */
        public function testSetUriDependency() {
            $Uri = $this->getMock('Brickoo\Http\Request\Interfaces\Uri');
            $RequestBuilder = new Request();
            $RequestBuilder->setUri($Uri);
            $this->assertAttributeSame($Uri, "Uri", $RequestBuilder);
        }

        /**
         * @covers Brickoo\Http\Builder\Request::setMethod
         */
        public function testSetRequestMethod() {
            $expectedValue = "PUT";
            $RequestBuilder = new Request();
            $RequestBuilder->setMethod($expectedValue);
            $this->assertAttributeEquals($expectedValue, "method", $RequestBuilder);
        }

        /**
         * @covers Brickoo\Http\Builder\Request::setMethod
         * @expectedException InvalidArgumentException
         */
        public function testSetRequestMethodThrowsInvalidArgumentException() {
            $RequestBuilder = new Request();
            $RequestBuilder->setMethod(array("wrongType"));
        }

        /**
         * @covers Brickoo\Http\Builder\Request::setVersion
         */
        public function testSetRequestProtocolVersion() {
            $expectedValue = \Brickoo\Http\Interfaces\Request::HTTP_VERSION_1_1;
            $RequestBuilder = new Request();
            $RequestBuilder->setVersion($expectedValue);
            $this->assertAttributeEquals($expectedValue, "version", $RequestBuilder);
        }

        /**
         * @covers Brickoo\Http\Builder\Request::setVersion
         * @expectedException InvalidArgumentException
         */
        public function testSetRequestVersionThrowsInvalidArgumentException() {
            $RequestBuilder = new Request();
            $RequestBuilder->setVersion(array("wrongType"));
        }

        /**
         * @covers Brickoo\Http\Builder\Request::build
         * @covers Brickoo\Http\Builder\Request::getHeader
         * @covers Brickoo\Http\Builder\Request::getBody
         * @covers Brickoo\Http\Builder\Request::getUri
         */
        public function testAutoBuild() {
            $RequestBuilder = new Request();
            $Request = $RequestBuilder->build();
            $this->assertInstanceOf('Brickoo\Http\Interfaces\Request', $Request);
        }

        /**
         * @covers Brickoo\Http\Builder\Request::build
         * @covers Brickoo\Http\Builder\Request::getHeader
         * @covers Brickoo\Http\Builder\Request::getBody
         * @covers Brickoo\Http\Builder\Request::getUri
         */
        public function testCustomBuild() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('Brickoo\Http\Request\Interfaces\Uri');
            $method = "POST";
            $version = \Brickoo\Http\Interfaces\Request::HTTP_VERSION_1_1;

            $RequestBuilder = new Request();
            $RequestBuilder->setHeader($Header)
                           ->setBody($Body)
                           ->setUri($Uri)
                           ->setMethod($method)
                           ->setVersion($version);

            $Request = $RequestBuilder->build();
            $this->assertInstanceOf('Brickoo\Http\Interfaces\Request', $Request);
            $this->assertAttributeSame($Header, "Header", $Request);
            $this->assertAttributeSame($Body, "Body", $Request);
            $this->assertAttributeSame($Uri, "Uri", $Request);
            $this->assertAttributeEquals($method, "method", $Request);
            $this->assertAttributeEquals($version, "version", $Request);
        }

    }