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

namespace Brickoo\Component\Http;

use ArrayIterator;
use Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException;
use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * HttpHeaderList
 *
 * Implementation of a http header list.
 */
class HttpHeaderList implements IteratorAggregate, Countable {

    /** @var array */
    private $elements;

    /**
     * Class constructor.
     * @param array $elements
     * @throws \InvalidArgumentException
     */
    public function __construct( array $elements = []) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\Http\\HttpHeader"))
            ->matches($elements)) {
            throw new InvalidArgumentException("HttpHeaderList must contain only HttpHeader elements.");
        }
        $this->elements = $elements;
    }

    /**
     * Add a http header to the list.
     * @param \Brickoo\Component\Http\HttpHeader $header
     * @return \Brickoo\Component\Http\HttpHeaderList
     */
    public function add(HttpHeader $header) {
        $this->elements[] = $header;
        return $this;
    }

    /**
     * Return the list element by list position.
     * @param integer $position
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @return \Brickoo\Component\Http\HttpHeader
     */
    public function get($position) {
        Argument::isInteger($position);
        if (! $this->has($position)) {
            throw new HeaderListElementNotAvailableException($position);
        }
        return $this->elements[$position];
    }

    /**
     * Check if an element is available
     * on a position.
     * @param integer $position
     * @return boolean check result
     */
    public function has($position) {
        Argument::isInteger($position);
        return isset($this->elements[$position]);
    }

    /**
     * Remove a element by its position from the list.
     * @param integer $position
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\HttpHeaderList
     */
    public function remove($position) {
        Argument::isInteger($position);
        if ($this->has($position)) {
            unset($this->elements[$position]);
        }
        return $this;
    }

    /**
     * Return the first element in list.
     * @throws \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @return HttpHeader
     */
    public function first() {
        return $this->get(0);
    }

    /**
     * Return the last element in list.
     * @throws \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @return HttpHeader
     */
    public function last() {
        return $this->get(count($this) - 1);
    }

    /**
     * Check if the list ist empty.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->elements);
    }

    /**
     * Return the list elements.
     * @return array list elements
     */
    public function toArray() {
        return $this->elements;
    }

    /**
     * Retrieve an external iterator
     * containing all elements.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

    /**
     * Count number of list elements.
     * @return integer the number of list elements.
     */
    public function count() {
        return count($this->elements);
    }

    /**
     * Return a string representation of the header list.
     * @return string the header in list
     */
    public function toString() {
        $headerRepresentation = "";

        foreach ($this as $header) {
            $headerRepresentation .= sprintf("%s\r\n", $header->toString());
        }

        return $headerRepresentation;
    }

}