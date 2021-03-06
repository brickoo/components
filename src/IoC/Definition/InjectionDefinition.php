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

use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use Brickoo\Component\Common\Assert;

/**
 * InjectionDefinition
 *
 * Implements a method injection definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InjectionDefinition {

    /** @const injection targets */
    const TARGET_CONSTRUCTOR = "constructor";
    const TARGET_METHOD = "method";
    const TARGET_PROPERTY = "property";

    /** @var string */
    private $target;

    /** @var string */
    private $targetName;

    /** @var \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer */
    private $argumentsContainer;

    /**
     * Class constructor.
     * @param string $target
     * @param string $targetName
     * @param \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer $container
     * @throws \InvalidArgumentException
     */
    public function __construct($target, $targetName, ArgumentDefinitionContainer $container) {
        Assert::isString($target);
        Assert::isString($targetName);
        $this->target = $target;
        $this->targetName = $targetName;
        $this->argumentsContainer = $container;
    }

    /**
     * Returns the injection target.
     * @return string the target
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Checks if the injection matches a target.
     * @param string $target
     * @return boolean check result
     */
    public function isTarget($target) {
        Assert::isString($target);
        return ($this->getTarget() == $target);
    }

    /**
     * Returns the injection target name.
     * @return string the target name
     */
    public function getTargetName() {
        return $this->targetName;
    }

    /**
     * Returns the dependency arguments container.
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function getArgumentsContainer() {
        return $this->argumentsContainer;
    }

}
