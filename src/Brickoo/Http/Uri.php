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
 * 2. Redistributionscd ..
 *  in binary form must reproduce the above copyright
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

namespace Brickoo\Http;

use Brickoo\Http\Authority,
    Brickoo\Http\Query,
    Brickoo\Validation\Argument;

/**
 * Uri
 *
 * Implements an uniform resource identifier.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Uri {

    /** @var string */
    private $scheme;

    /** @var \Brickoo\Http\Authority */
    private $authority;

    /** @var string */
    private $path;

    /** @var \Brickoo\Http\Query */
    private $query;

    /** @var string */
    private $fragment;

    /**
     * Class constructor.
     * @param string $scheme the uri protocol scheme
     * @param \Brickoo\Http\Authority $authority
     * @param string $path the uri path
     * @param \Brickoo\Http\Query $Query
     * @param string $fragment
     * @return void
     */
    public function __construct($scheme, Authority $authority, $path, Query $query, $fragment) {
        Argument::IsString($scheme);
        Argument::IsString($path);
        Argument::IsString($fragment);

        $this->scheme = $scheme;
        $this->authority = $authority;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * Returns the request uri protocol scheme.
     * @return string the uri scheme
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Returns the uri authority component.
     * @return \Brickoo\Http\Authority
     */
    public function getAuthority() {
        return $this->authority;
    }

    /**
     * Returns the uri path.
     * @return string the uri path
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the uri query component.
     * @return \Brickoo\Http\Query
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Returns the uri fragment.
     * @return string the uri fragment
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * Returns the string representation of the uri.
     * @return string the uri representation
     */
    public function toString() {
        $uriParts = sprintf("%s://%s", $this->getScheme(), $this->getAuthority()->toString());

        if ($queryString = $this->getQuery()->toString()) {
            $queryString = "?". $queryString;
        }

        if ($fragment = $this->getFragment()) {
            $fragment = "#". $fragment;
        }

        return  $uriParts . $this->getPath() . $queryString . $fragment;
    }

 }