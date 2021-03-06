<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Http\Header\Aggregator\Strategy;

use Brickoo\Component\Http\Header\Aggregator\Strategy\PhpHeaderFieldsAggregatorStrategy;
use PHPUnit_Framework_TestCase;

/**
 * PhpHeaderFieldsAggregatorStrategy
 *
 * Test suite for the PhpHeaderFieldsAggregatorStrategy class.
 * @see Brickoo\Component\Http\Header\Aggregator\PhpHeaderFieldsAggregatorStrategy
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class PhpHeaderAggregatorStrategyTest extends PHPUnit_Framework_TestCase {

    /**
     * @cover Brickoo\Component\Http\Header\Aggregator\PhpHeaderFieldsAggregatorStrategy::getHeaderFields
     * @cover Brickoo\Component\Http\Header\Aggregator\PhpHeaderFieldsAggregatorStrategy::getPhpExtractedHttpHeaders
     */
    public function testGetHeadersFromGlobalServerValues() {
        if (!function_exists("apache_request_headers")) {
            require_once realpath(__DIR__)."/Assets/requiredFunctions.php";
        }

        $expectedHeaders = ["CONNECTION" => "keep-alive", "X-Unit-Test" => "ok"];
        $serverVars = ["HTTP_CONNECTION" => "keep-alive"];
        $requestHeaderAggregatorStrategy = new PhpHeaderFieldsAggregatorStrategy($serverVars);
        $this->assertEquals($expectedHeaders, $requestHeaderAggregatorStrategy->getHeaderFields());
    }

}
