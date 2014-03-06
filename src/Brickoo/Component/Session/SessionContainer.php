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

namespace Brickoo\Component\Session;

use Brickoo\Component\Validation\Argument;

/**
 * SessionContainer
 *
 * Implements a session object based on namespaces which should prevent naming conflicts.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SessionContainer {

    /** @var string */
    protected $sessionNamespace;

    /**
     * Class constructor.
     * @param string $sessionNamespace the namespace to use
     * @return void
     */
    public function __construct($sessionNamespace) {
        Argument::IsString($sessionNamespace);
        $this->sessionNamespace = $sessionNamespace;
    }

    /**
    * Checks if the session property is available.
    * @param string $property the property to check in the session
    * @return boolean check result
    */
    public function has($property) {
        Argument::IsString($property);

        return array_key_exists($this->getNamespace($property), $_SESSION);
    }

    /**
     * Returns the session property holded content or the default value.
     * @param string $property the session property to retrieve the content from
     * @param mixed $defaultValue the default value if the property des not exist
     * @return mixed the property holded content or the default value if the property does not exist
     */
    public function get($property, $defaultValue = null) {
        Argument::IsString($property);

        if (! $this->has($property)) {
            return $defaultValue;
        }

        return $_SESSION[$this->getNamespace($property)];
    }

    /**
     * Sets the session property and assigns the content to it.
     * @param string $property the property to assign the content to
     * @param mixed $value the value to store
     * @return \Brickoo\Component\Http\Session\Session
     */
    public function set($property, $value) {
        Argument::IsString($property);

        $_SESSION[$this->getNamespace($property)] = $value;

        return $this;
    }

    /**
     * Removes the session property if available.
     * @param string $property the property to remove
     * @return \Brickoo\Component\Http\Session\Session
     */
    public function remove($property) {
        Argument::IsString($property);

        if ($this->has($property)) {
            unset($_SESSION[$this->getNamespace($property)]);
        }

        return $this;
    }

    /**
     * Returns the property namespace name.
     * @param string $property the property to modify
     * @return string the session namespace of the property
     */
    private function getNamespace($property) {
        return $this->sessionNamespace .".". $property;
    }

}