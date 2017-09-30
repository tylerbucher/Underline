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
 * Class CookieModule provides an easy interface to handle cookies with default options.
 * @package Underline\Module
 */
class CookieModule implements IModule, IStorageModule {

    /**
     * @var ConfigModule The configuration module to use.
     */
    private $configModule;

    /**
     * CookieModule constructor.
     *
     * @param ConfigModule $configModule The configuration module to use.
     */
    public function __construct(ConfigModule $configModule) {
        $this->configModule = $configModule;
    }

    /**
     * Require all files that this module will be using.
     */
    public function require(): void {
        // Nothing to be required at this time.
    }

    /**
     * Initialize all required properties and functions for the Module.
     */
    public function init(): void {
        // Nothing to be initialized at this time.
    }

    /**
     * Note about cookie time:
     * @link http://us3.php.net/manual/en/function.setcookie.php#109173 Why not to use time().
     * @link http://us3.php.net/manual/en/function.setcookie.php#110193 What else to use.
     *
     * @param string      $name      The name of the cookie.
     * @param mixed       $value     The value of the cookie. This value is stored on the clients computer; do not store sensitive information.
     * @param int|null    $time      The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
     * @param string|null $path      The path on the server in which the cookie will be available on. If set to '/', the cookie will be available within the entire domain.
     * @param string|null $subDomain he (sub)domain that the cookie is available to.
     * @param int|null    $sslOnly   Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client.
     * @param int|null    $httpOnly  When TRUE the cookie will be made accessible only through the HTTP protocol.
     */
    public function setCookie(string $name, $value, int $time = null, string $path = null, string $subDomain = null, int $sslOnly = null, int $httpOnly = null) {
        if ($time == null) $time = time() * $this->configModule->getCookieDefaultExpireTime();
        if ($path == null) $path = $this->configModule->getCookieDefaultPath();
        if ($subDomain == null) $subDomain = $this->configModule->getCookieDefaultSubDomain();
        if ($sslOnly == null) $sslOnly = $this->configModule->getCookieDefaultSsl();
        if ($httpOnly == null) $httpOnly = $this->configModule->getCookieDefaultHttp();
        setcookie($name, $value, $time, $path, $subDomain, $sslOnly, $httpOnly);
    }

    /**
     * @param string $name The name of the cookie.
     *
     * @return mixed|null The cookie if found otherwise null.
     */
    public function getCookie(string $name) {
        $wantedCookie = $_COOKIE[$name];
        return isset($wantedCookie) ? $wantedCookie : null;
    }

    /**
     * @param string      $name The name of the cookie.
     * @param string|null $path The path on the server in which the cookie will be available on. If set to '/', the cookie will be available within the entire domain.
     *
     * @return bool True if the cookie was removed false otherwise.
     */
    public function removeCookie(string $name, string $path = null) {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            $this->setCookie($name, '', $this->configModule->getCookieDefaultRemoveTime(), $path);
            return true;
        }
        return false;
    }
}