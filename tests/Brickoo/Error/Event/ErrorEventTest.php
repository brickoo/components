<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Error;

    use Brickoo\Error\Event\ErrorEvent;

    /**
     * ErrorEventTest
     *
     * Test suite for the ErrorEvent class.
     * @see Brickoo\Error\Event\ErrorEvent
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ErrorEventTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Error\Event\ErrorEvent::__construct
         */
        public function testContructor() {
            $errorMessage = "An error occurred.";
            $errorStacktrace = "Somefile.php on line 10.";

            $expectedParameters = array(
                ErrorEvent::PARAM_ERROR_MESSAGE => $errorMessage,
                ErrorEvent::PARAM_ERROR_STACKTRACE => $errorStacktrace
            );

            $ErrorEvent = new ErrorEvent($errorMessage, $errorStacktrace);
            $this->assertInstanceOf('Brickoo\Event\Interfaces\Event', $ErrorEvent);
            $this->assertInstanceOf('Brickoo\Error\Event\Interfaces\ErrorEvent', $ErrorEvent);
            $this->assertAttributeEquals(\Brickoo\Error\Events::ERROR, "name", $ErrorEvent);
            $this->assertAttributeEquals($expectedParameters, "params", $ErrorEvent);
        }

        /**
         * @covers Brickoo\Error\Event\ErrorEvent::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorStacktraceArgumentThrowsException() {
            $ErrorEvent = new ErrorEvent("An error occurred.", array("wrongType"));
        }

        /**
         * @covers Brickoo\Error\Event\ErrorEvent::getErrorMessage
         */
        public function testGetErrorMessage() {
            $errorMessage = "An error occurred.";

            $ErrorEvent = new ErrorEvent($errorMessage);
            $this->assertEquals($errorMessage, $ErrorEvent->getErrorMessage());
        }

        /**
         * @covers Brickoo\Error\Event\ErrorEvent::getErrorStacktrace
         */
        public function testGetErrorStacktrace() {
            $errorStacktrace = "Somefile.php on line 10.";

            $ErrorEvent = new ErrorEvent("An error occurrred.", $errorStacktrace);
            $this->assertEquals($errorStacktrace, $ErrorEvent->getErrorStacktrace());
        }

    }