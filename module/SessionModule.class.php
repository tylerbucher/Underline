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

/**
 * Class SessionModule attempts to create a object-oriented way of handling sessions.
 * @package Underline\Module
 */
class SessionModule implements IModule, IStorageModule {

    /**
     * @var ConfigModule The configuration module to use.
     */
    private $configModule;

    /**
     * SessionModule constructor.
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
    public function init(array $args): void {
        // Set the php session name
        session_name($this->configModule->getSessionName());
        // Best way to start php session for PHP >= 5.4.0 (https://stackoverflow.com/a/18542272/2949095)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param string      $key       The key to place the value at in the session.
     * @param mixed       $value     The value to store in the session.
     * @param string|null $namespace If this is left out this library with use the sessionDefaultNamespace from ConfigModule.
     */
    public function setData(string $key, $value, string $namespace = null): void {
        if ($namespace == null) $namespace = $this->configModule->getSessionDefaultNamespace();
        $_SESSION[$namespace][$key] = $value;
    }

    /**
     * @param string      $key       The key to fetch the value at in the session.
     * @param string|null $namespace If this is left out this library with use the sessionDefaultNamespace from ConfigModule.
     *
     * @return mixed The value stored in the session.
     */
    public function getData(string $key, string $namespace = null) {
        if ($namespace == null) $namespace = $this->configModule->getSessionDefaultNamespace();
        return $_SESSION[$namespace][$key];
    }

    /**
     * @param string|null $namespace If this is left out this library with use the sessionDefaultNamespace from ConfigModule.
     * @param string|null $key       The key to fetch the value at in the session.
     */
    public function clearData(string $namespace = null, string $key = null): void {
        if ($namespace == null) {
            // Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session
            // variables through the $_SESSION superglobal.
            // http://php.net/manual/en/function.session-unset.php
            session_unset();
        } else if ($key == null) {
            unset($_SESSION[$namespace]);
        } else {
            unset($_SESSION[$namespace][$key]);
        }
    }

    /**
     * Destroys the session.
     */
    public function destroySession(): void {
        session_destroy();
    }
}