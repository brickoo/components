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

namespace Brickoo\Tests\Http\Resolver\Loader;

use Brickoo\Http\Resolver\Loader\StringHeaderResolverLoader,
    PHPUnit_Framework_TestCase;

/**
 * StringHeaderResolverLoaderTest
 *
 * Test suite for the StringHeaderResolverLoader class.
 * @see Brickoo\Http\Resolver\StringHeaderResolverLoader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class StringHeaderResolverLoaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers  Brickoo\Http\Resolver\Loader\StringHeaderResolverLoader::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHeaderStringThrowsException() {
        new StringHeaderResolverLoader(["wrongType"]);
    }

    /**
     * @covers  Brickoo\Http\Resolver\Loader\StringHeaderResolverLoader::__construct
     * @covers  Brickoo\Http\Resolver\Loader\StringHeaderResolverLoader::getHeaders
     */
    public function testGetHeadersFromString() {
        $expectedHeaders = ["Accept" => "*/*", "Connection" => "keep-alive"];
        $stringHeaderResolverLoader = new StringHeaderResolverLoader("Accept: */*\r\nConnection: keep-alive\r\n");
        $this->assertEquals($expectedHeaders, $stringHeaderResolverLoader->getHeaders());
        //
    }

}