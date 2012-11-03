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

    namespace Tests\Brickoo\Validator\Constraint;

    use Brickoo\Validator\Constraint\TraversableContainsType;

    require_once "Fixture/TraversableFixture.php";

    /**
     * TraversableContainsTypeTest
     *
     * Test suite for the TraversableContainsType class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TraversableContainsTypeTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsType::__construct
         */
        public function testConstructor() {
            $callFunctionName = "string";

            $TraversableContainsType = new TraversableContainsType($callFunctionName);
            $this->assertInstanceOf('Brickoo\Validator\Constraint\Interfaces\Constraint', $TraversableContainsType);
            $this->assertAttributeEquals("is_". $callFunctionName, 'callFunctionName', $TraversableContainsType);
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsType::assert
         */
        public function testAssertionOfTraversableValues() {
            $compareFrom = new Fixture\TraversableFixture();

            $TraversableContainsType = new TraversableContainsType("string");
            $this->assertTrue($TraversableContainsType->assert($compareFrom));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsType::assert
         */
        public function testAssertionOfArrayValues() {
            $compareFrom = array("test", "case");

            $TraversableContainsType = new TraversableContainsType("string");
            $this->assertTrue($TraversableContainsType->assert($compareFrom));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsType::assert
         */
        public function testAssertionOfOneValueFailure() {
            $compareFrom = array("test", 123);

            $TraversableContainsType = new TraversableContainsType("string");
            $this->assertFalse($TraversableContainsType->assert($compareFrom));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsType::assert
         * @expectedException InvalidArgumentException
         */
        public function testAssertionThrowsInvalidArgumentException() {
            $TraversableContainsType = new TraversableContainsType("string");
            $TraversableContainsType->assert(new \stdClass());
        }

    }