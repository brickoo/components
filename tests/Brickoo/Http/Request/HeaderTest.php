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

    namespace Tests\Brickoo\Http\Request;

    use Brickoo\Http\Request\Header;

    /**
     * HeaderTest
     *
     * Test suite for the Request\Header class.
     * @see Brickoo\Http\Request\Header
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HeaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Request\Header::__construct
         */
        public function testConstructor() {
            $Header = new Header();
            $this->assertInstanceOf("Brickoo\Http\Request\Interfaces\Header", $Header);
            $this->assertAttributeInternalType("array", "acceptTypes", $Header);
            $this->assertAttributeInternalType("array", "acceptCharsets", $Header);
            $this->assertAttributeInternalType("array", "acceptLanguages", $Header);
            $this->assertAttributeInternalType("array", "acceptEncodings", $Header);
        }

        /**
         * @covers Brickoo\Http\Request\Header::getAcceptTypes
         * @covers Brickoo\Http\Request\Header::getAcceptHeaderByRegex
         */
        public function testGetAcceptTypes() {
            $expectedTypes = array(
                "*/*"                      => 0.8,
                "application/xml"          => 0.9,
                "application/xhtml+xml"    => 1,
                "text/html"                => 1
            );
            $Header = new Header();
            $Header->set("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8");
            $this->assertEquals($expectedTypes, $Header->getAcceptTypes());
        }

        /**
         * @covers Brickoo\Http\Request\Header::isTypeSupported
         */
        public function testIsTypeSupported() {
            $Header = new Header();
            $Header->set("Accept", "text/html,application/xml;q=0.9,*/*;q=0.8");
            $this->assertTrue($Header->isTypeSupported("application/xml"));
            $this->assertTrue($Header->isTypeSupported("text/html"));
            $this->assertTrue($Header->isTypeSupported("*/*"));
        }

        /**
         * @covers Brickoo\Http\Request\Header::isTypeSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsTypeSupportedArgumentException() {
            $Header = new Header();
            $Header->isTypeSupported(null);
        }

        /**
         * @covers Brickoo\Http\Request\Header::getAcceptLanguages
         * @covers Brickoo\Http\Request\Header::getAcceptHeaderByRegex
         */
        public function testGetAcceptLanguages() {
            $expectedLanguages = array(
                "de-DE" => 1,
                "de"    => 0.8,
                "en-US" => 0.6,
                "en"    => 0.4
            );
            $Header = new Header();
            $Header->set("Accept-Language", "de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4");
            $this->assertEquals($expectedLanguages, $Header->getAcceptLanguages());
        }

        /**
         * @covers Brickoo\Http\Request\Header::isLanguageSupported
         */
        public function testIsLanguageSupported() {
            $Header = new Header();
            $Header->set("Accept-Language", "de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4");
            $this->assertTrue($Header->isLanguageSupported("de-DE"));
            $this->assertTrue($Header->isLanguageSupported("de"));
            $this->assertTrue($Header->isLanguageSupported("en-US"));
            $this->assertTrue($Header->isLanguageSupported("en"));
        }

        /**
         * @covers Brickoo\Http\Request\Header::isLanguageSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsLanguageSupportedArgumentException() {
            $Header = new Header();
            $Header->isLanguageSupported(null);
        }

        /**
         * @covers Brickoo\Http\Request\Header::getAcceptEncodings
         * @covers Brickoo\Http\Request\Header::getAcceptHeaderByRegex
         */
        public function testGetAcceptEncodings() {
            $expectedEncodings = array(
                "gzip"       => 1,
                "deflate"    => 1,
                "special"    => 0.1
            );
            $Header = new Header();
            $Header->set("Accept-Encoding", "gzip,deflate,special;q=0.1");
            $this->assertEquals($expectedEncodings, $Header->getAcceptEncodings());
        }

        /**
         * @covers Brickoo\Http\Request\Header::isEncodingSupported
         */
        public function testIsEncodingSupported() {
            $Header = new Header();
            $Header->set("Accept-Encoding", "gzip,deflate,special;q=0.1");
            $this->assertTrue($Header->isEncodingSupported("deflate"));
            $this->assertTrue($Header->isEncodingSupported("gzip"));
            $this->assertTrue($Header->isEncodingSupported("special"));
        }

        /**
         * @covers Brickoo\Http\Request\Header::isEncodingSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsEncodingSupportedArgumentException() {
            $Header = new Header();
            $Header->isEncodingSupported(null);
        }

        /**
         * @covers Brickoo\Http\Request\Header::getAcceptCharsets
         * @covers Brickoo\Http\Request\Header::getAcceptHeaderByRegex
         */
        public function testGetAcceptCharsets() {
            $expectedCharsets = array(
                "ISO-8859-1"   => 1,
                "utf-8"        => 0.7,
                "*"            => 0.3
            );
            $Header = new Header();
            $Header->set("Accept-Charset", "ISO-8859-1,utf-8;q=0.7,*;q=0.3");
            $this->assertEquals($expectedCharsets, $Header->getAcceptCharsets());
        }

        /**
         * @covers Brickoo\Http\Request\Header::isCharsetSupported
         */
        public function testIsCharsetSupported() {
            $Header = new Header();
            $Header->set("Accept-Charset", "ISO-8859-1,utf-8;q=0.7,*;q=0.3");
            $this->assertTrue($Header->isCharsetSupported("ISO-8859-1"));
            $this->assertTrue($Header->isCharsetSupported("utf-8"));
            $this->assertTrue($Header->isCharsetSupported("*"));
        }

        /**
         * @covers Brickoo\Http\Request\Header::isCharsetSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsCharsetSupportedArgumentException() {
            $Header = new Header();
            $Header->isCharsetSupported(array("wrongType"));
        }

    }