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

namespace Underline;

/**
 * Class HtmlElement, provides a nice object-oriented way to construct html elements.
 * @package Underline
 */
class HtmlElement {

    /**
     * @var string The html element tag.
     */
    private $tag;

    /**
     * @var array A list of all html attributes that this html element will use.
     */
    private $attributes = array();

    /**
     * @var string The value if any for the html element.
     */
    private $value;

    /**
     * @var HtmlElement[] The list of child elements contained with in this HtmlElement.
     */
    private $childElements;

    /**
     * HtmlElement constructor.
     *
     * @param string $tag           The html element tag.
     * @param array  $attributes    A list of all html attributes that this html element will use.
     * @param array  $childElements The list of child elements contained with in this HtmlElement.
     * @param string $value         The value if any for the html element.
     */
    public function __construct(string $tag, array $attributes, array $childElements, $value = '') {
        $this->tag = htmlspecialchars($tag);
        $this->setAttributes($attributes);
        $this->childElements = $childElements;
        $this->value = $value;
    }

    /**
     * @return array Return the html elements attributes.
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param string $key   The key of the attribute.
     * @param mixed  $value The value of the attribute for the HTML element.
     *
     * @return HtmlElement this html component.
     */
    public function setAttribute(string $key, $value) {
        $this->attributes[$key] = htmlspecialchars($value);
        return $this;
    }


    /**
     * @param array $attributes The attributes for the html element.
     *
     * @return $this html element.
     */
    public function setAttributes(array $attributes) {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * @return mixed The set value of the html element if any.
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param $value mixed The value of the html element to set.
     *
     * @return $this html element.
     */
    public function setValue($value) {
        $this->value = htmlspecialchars($value);
        return $this;
    }

    /**
     * @return mixed The child elements of this html element.
     */
    public function getChildElements() {
        return $this->childElements;
    }

    /**
     * @param $childElements HtmlElement[] The child elements of this html element.
     *
     * @return $this html element.
     */
    public function setChildElements(array $childElements) {
        $this->childElements = $childElements;
        return $this;
    }

    /**
     * Export / print the html element.
     */
    public function exportHtml() {
        // This is faster then string concatenation. https://www.electrictoolbox.com/php-echo-commas-vs-concatenation/
        echo '<', $this->tag, ' ', vsprintf('%k="%v" ', $this->attributes), '>';
        // Print value or child elements
        if (empty($this->subHtmlComponents)) echo $this->value;
        else foreach ($this->childElements as /** @var HtmlElement */
                      $childElements) $childElements->exportHtml();
        // Close HTML tag
        echo '</', $this->tag, '>';
    }
}