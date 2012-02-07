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

    namespace Brickoo\Library\Http\Response;

    use Brickoo\Library\Validator\TypeValidator;

    /**
     * PhpResponse
     *
     * Implements a PHP template response which makes use of PHP based templates.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class PhpResponse implements Interfaces\ResponseInterface
    {

        /**
         * Holds the ResponseHeaders dependency:
         * @var Bricko\Library\Http\Interfaces\ResponseHeadersInterface
         */
        protected $ResponseHeaders;

        /**
         * Lazy initialization of the ResponseHeaders dependency.
         * Returns the ResponseHeaders dependency.
         * @return \Bricko\Library\Http\Interfaces\ResponseHeadersInterface
         */
        public function getResponseHeaders()
        {
            if (! $this->ResponseHeaders instanceof Interfaces\ResponseHeadersInterface) {
                $this->injectResponseHeaders(new ResponseHeaders());
            }

            return $this->ResponseHeaders;
        }

        /**
         * Injects the ResponseHeaders dependency.
         * @param \Brickoo\Library\Http\Interfaces\ResponseHeadersInterface $ResponseHeaders the ResponseHeaders dependency
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return \Brickoo\Library\Http\Response\PhpResponse
         */
        public function injectResponseHeaders(\Brickoo\Library\Http\Interfaces\ResponseHeadersInterface $ResponseHeaders)
        {
            if ($this->ResponseHeaders !== null) {
                throw new Core\Exceptions\DependencyOverwriteException('ResponseHeadersInterface');
            }

            $this->ResponseHeaders = $ResponseHeaders;

            return $this;
        }

        /**
         * Holds the full path of the used template file.
         * @var string
         */
        protected $templateFile;

        /**
         * Returns the template file holded.
         * @return string the full path to the template file
         */
        public function getTemplateFile()
        {
            return $this->templateFile;
        }

        /**
         * Sets the full path to template file.
         * @param string $templateFile the full path and template file name.
         * @throws Exceptions\TemplateFileDoesNotExist if the template is not available
         * @return \Brickoo\Library\Http\Response\PhpResponse
         */
        public function setTemplateFile($templateFile)
        {
            TypeValidator::IsString($templateFile);

            if (! file_exists($templateFile)) {
                throw new Exceptions\TemplateFileDoesNotExist($templateFile);
            }

            $this->templateFile = $templateFile;

            return $this;
        }

        /**
         * Cehcks if the template file is set.
         * @return boolean check result
         */
        public function hasTemplateFile()
        {
            return ($this->templateFile !== null);
        }

        /**
         * Holds the template variables assigned.
         * @var array
         */
        protected $templateVars;

        /**
         * Adds template variables to make available in the template file.
         * @param array $variables the variables used in the template
         * @return \Brickoo\Library\Http\Response\PhpResponse
         */
        public function addTemplateVars(array $variables)
        {
            TypeValidator::IsArray($variables);

            $this->templateVars = array_merge($this->templateVars, $variables);

            return $this;
        }

        /**
         * Returns all templates variables if the argument is null
         * otherwise retreives the value of the variable.
         * @param string|null $variable the variable to retrieve the value from
         * @throws \UnexpectedValueException if the variable is not set
         * @return mixed array the template variables or the template variable value
         */
        public function getTemplateVar($variable = null)
        {
            if ($variable === null) {
                return $this->templateVars;
            }

            TypeValidator::IsString($variable);

            if (! $this->hasTemplateVars($variable)) {
                throw new \UnexpectedValueException(sprintf('The template variable `%s` does not exist.', $variable));
            }

            return $this->templateVars[$variable];
        }

        /**
         * Checks if the template has variables assigned
         * or if the passed variable is available.
         * @param string||null $variable the variable to check the avaibility
         * @return boolean check result
         */
        public function hasTemplateVars($variable = null)
        {
            if ($variable === null) {
                return (! empty($this->templateVars));
            }

            TypeValidator::IsString($variable);

            return array_key_exists($variable, $this->templateVars);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->templateFile    = null;
            $this->templateVars    = array();
        }

        /**
         * Renders the template with the assigned variables.
         * @see Brickoo\Library\Http\Interfaces.ResponseInterface::render()
         * @throws \UnexpectedValueException if the template file is not set
         * @return string the rendered content
         */
        public function render()
        {
            if (! $this->hasTemplateFile()) {
                throw new \UnexpectedValueException('The template file is not set.');
            }

            if ($this->hasTemplateVars()) {
                $P = $this->getTemplateVar();

                extract($P, EXTR_SKIP);
            }

            ob_start();
            require ($this->getTemplateFile());
            return ob_get_clean();
        }

        /**
         * Magic fucntion to retrieve the value of a template variable.
         * @param string $variable the varaiable to retrieve the value from
         * @return mixed the template variable value
         */
        public function __get($variable)
        {
            return $this->getTemplateVar($variable);
        }

        /**
         * Magic function to set a template variable.
         * If the variable exists the value will be overwritten.
         * @param string $variable the variable to set
         * @param mixed $value the value of the variable
         */
        public function __set($variable, $value)
        {
            TypeValidator::IsString($variable);

            $this->templateVars[$variable] = $value;
        }

    }