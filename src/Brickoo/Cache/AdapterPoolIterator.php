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

namespace Brickoo\Cache;

use Brickoo\Validator\Constraint\TraversableContainsInstancesOf,
    Brickoo\Validator\Argument;

/**
 * AdapterPoolIterator
 *
 * Implementation of an iterable caching adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AdapterPoolIterator implements \Iterator, \Countable, AdapterPool {

    /** @var array containing entries of type \Brickoo\Cache\Adapter */
    private $poolEntries;

    /** @var array */
    private $mappingKeys;

    /** @var integer */
    private $currentAdapterIdentifier;

    /**
     * Class constructor.
     * @param array $poolEntries
     * @throws \InvalidArgumentException if a pool entry does not match expected type
     * @return void
     */
    public function __construct(array $poolEntries) {
        if (! (new TraversableContainsInstancesOf("\\Brickoo\\Cache\\Adapter"))->assert($poolEntries)) {
            throw new \InvalidArgumentException(
                "The pool entries must be instances implementing \\Brickoo\\Cache\\Adapter interface."
            );
        }

        $this->poolEntries = array_values($poolEntries);
        $this->mappingKeys = array_keys($poolEntries);
        $this->currentAdapterIdentifier = 0;
    }

     /**
     * Checks if the current pool adapter entry is ready.
     * @throws \Brickoo\Cache\Exception\PoolIsEmpty
     * @return boolean check result
     */
    public function isCurrentReady() {
        if ($this->isEmpty()) {
            throw new Exception\PoolIsEmptyException();
        }

        return $this->current()->isReady();
    }

    /**
     * {@inheritDoc}
     * @return \Brickoo\Cache\Adapter
     */
    public function current() {
        return $this->poolEntries[$this->currentAdapterIdentifier];
    }

    /**
     * {@inheritDoc}
     * @return string the current pool key
     */
    public function key() {
        if (isset($this->mappingKeys[$this->currentAdapterIdentifier])) {
            return $this->mappingKeys[$this->currentAdapterIdentifier];
        }
        return (string)$this->currentAdapterIdentifier;
    }

    /** {@inheritDoc} */
    public function next() {
        $this->currentAdapterIdentifier++;
    }

    /** {@inheritDoc} */
    public function rewind() {
        $this->currentAdapterIdentifier = 0;
    }

    /** {@inheritDoc} */
    public function valid() {
        return isset($this->poolEntries[$this->currentAdapterIdentifier]);
    }

    /** {@inheritDoc} */
    public function select($adapterIdentifier) {
        Argument::IsStringOrInteger($adapterIdentifier);

        if (! $this->has($adapterIdentifier)) {
            throw new Exception\PoolIndentifierDoesNotExistException($adapterIdentifier);
        }

        $this->currentAdapterIdentifier = array_search($adapterIdentifier, $this->mappingKeys, true);
        return $this;
    }

    /** {@inheritDoc} */
    public function remove($adapterIdentifier) {
        if (! $this->has($adapterIdentifier)) {
            throw new Exception\PoolIndentifierDoesNotExistException($adapterIdentifier);
        }

        //
    }

    /** {@inheritDoc} */
    public function has($adapterIdentifier) {
        Argument::IsStringOrInteger($adapterIdentifier);
        return in_array($adapterIdentifier, $this->mappingKeys, true);
    }

    /** {@inheritDoc} */
    public function isEmpty() {
        return empty($this->poolEntries);
    }

    /** {@inheritDoc} */
    public function count() {
        return count($this->poolEntries);
    }

}