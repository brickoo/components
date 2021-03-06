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

namespace Brickoo\Component\IoC\Definition;

use Brickoo\Component\Common\Assert;

/**
 * ArgumentDefinition
 *
 * Implements a parameter definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArgumentDefinition {

    /** @var string */
    private $name;

    /** @var mixed */
    private $value;

    /**
     * Class constructor.
     * @param mixed $value
     * @param string $name
     */
    public function __construct($value, $name = "") {
        Assert::isString($name);
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Checks if the argument has a name.
     * @return boolean check result
     */
    public function hasName() {
        return (!empty($this->name));
    }

    /**
     * Returns the parameter name.
     * @return string the parameter name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the parameter value.
     * @return mixed the parameter value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the parameter value.
     * @param mixed $value
     * @return \Brickoo\Component\IoC\Definition\ArgumentDefinition
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

}
