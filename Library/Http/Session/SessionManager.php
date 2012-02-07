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

    namespace Brickoo\Library\Http\Session;

    use Brickoo\Library\Validator\TypeValidator;

    /**
     * SessionManager
     *
     * Manages session operations with a session handler dependency
     * implementing the Session\Interfaces\SessionHandlerInterface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SessionManager implements Interfaces\SessionManagerInterface
    {

        /**
         * Holds a flag for the session started status.
         * @var boolean
         */
        protected static $SessionStarted;

        /**
         * Checks if the session has been started.
         * @return boolean check result
         */
        public function hasSessionStarted()
        {
            return (self::$SessionStarted === true);
        }

        /**
         * Holds an instance of the session handler implementing the Cache\Interfaces\SessionHandlerInterface.
         * @var object
         */
        protected $SessionHandler;

        /**
         * Registers the session handler.
         * @param \Brickoo\Library\Http\Session\Handler\Interfaces\SessionHandlerInterface $SessionHandler the session hadnler to register
         * @return boolean success
         */
        protected function registerSessionHandler(\Brickoo\Library\Http\Session\Handler\Interfaces\SessionHandlerInterface $SessionHandler)
        {
            $this->SessionHandler = $SessionHandler;

            return session_set_save_handler(
                array($SessionHandler, 'open'),
                array($SessionHandler, 'close'),
                array($SessionHandler, 'read'),
                array($SessionHandler, 'write'),
                array($SessionHandler, 'destroy'),
                array($SessionHandler, 'gc')
            );
        }

        /**
         * Overwrites the session cookie parameters.
         * @param array $cookieParameters the cookie parameters to overwrite
         * @return array the cookie parameters set
         */
        public function setCookieParameters(array $cookieParameters)
        {
            $cookieParameters = array_merge(session_get_cookie_params(), $cookieParameters);

            session_set_cookie_params(
                $cookieParameters['lifetime'],
                $cookieParameters['path'],
                $cookieParameters['domain'],
                $cookieParameters['secure'],
                $cookieParameters['httponly']
            );

            $this->SessionHandler->setLifetime($cookieParameters['lifetime']);

            return $cookieParameters;
        }

        /**
         * Sets the session configuration.
         * Available configuration keys are [id, name, limiter].
         * @param array $configuration the configuration to replace
         * @return \Brickoo\Library\Http\Session\SessionManager
         */
        public function setSessionConfiguration(array $configuration)
        {
            if (! $this->hasSessionStarted()) {
                foreach($configuration as $option => $value) {
                    if ($option == 'name') {
                        session_name($value);
                        continue;
                    }
                    if ($option == 'limiter') {
                        session_cache_limiter($value);
                        continue;
                    }
                }
            }

            return $this;
        }

        /**
         * Class constructor.
         * Registers the session handler.
         * Sets the default session lifetime to the session handler.
         * Sets the default session configuration used.
         * @param \Brickoo\Library\Http\Session\Handler\Interfaces\SessionHandlerInterface $SessionHandler the SessionHandler to inject
         * @return void
         */
        public function __construct(\Brickoo\Library\Http\Session\Handler\Interfaces\SessionHandlerInterface $SessionHandler)
        {
            if (! $this->hasSessionStarted()) {
                $this->registerSessionHandler($SessionHandler);
                $cookieParameters = session_get_cookie_params();
                $this->SessionHandler->setLifetime($cookieParameters['lifetime']);
                $this->setSessionConfiguration(array('name' => 'BOO', 'limiter' => false));
            }
        }

        /**
         * Starts the Session.
         * @return \Brickoo\Library\Http\Session\SessionManager
         */
        public function start()
        {
            if (! $this->hasSessionStarted()) {
                session_start();
                self::$SessionStarted = true;
            }

            return $this;
        }

        /**
         * Stops the session and calls for writing and close.
         * @return \Brickoo\Library\Http\Session\SessionManager
         */
        public function stop()
        {
            if ($this->hasSessionStarted()) {
                session_write_close();
                self::$SessionStarted = false;
            }

            return $this;
        }

    }