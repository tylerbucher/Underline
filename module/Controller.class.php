<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Tyler Bucher
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

namespace Underline\Module;

use Underline\HtmlElement;

/**
 * Class Controller should be extended and expanded upon in the executing application.
 * @package Underline\Module
 */
class Controller implements IModule {

    /**
     * @var ConfigModule The configuration module to use.
     */
    private $configModule;

    /**
     * @var HtmlElement[] A list of html elements to be passed to the template.
     */
    private $baseElements = array();

    /**
     * Controller constructor.
     *
     * @param ConfigModule $configModule The configuration module to use.
     */
    public function __construct(ConfigModule $configModule) {
        $this->configModule = $configModule;
    }

    /**
     * Initialize all required properties and functions for the Module.
     *
     * @param array $args A list if arguments if needed.
     */
    public function init(array $args = null): void {
        // Nothing to be initialized at this time.
    }

    /**
     * @return ConfigModule The config module.
     */
    public function getConfigModule(): ConfigModule {
        return $this->configModule;
    }

    /**
     * @return HtmlElement[] A list of html elements to be passed to the template.
     */
    public function getBaseElements(): array {
        return $this->baseElements;
    }

    /**
     * @param HtmlElement[] $baseElements A list of html elements to be passed to the template.
     */
    public function setBaseElements(array $baseElements): void {
        $this->baseElements = $baseElements;
    }
}