<?php

/**
 * Class ToArrayAndJson
 * @package Commune\Chatbot\Framework\Support
 */

namespace Commune\Support\Arr;

/**
 * 许多场景都需要进行数组化和序列化, 因此添加工具降低代码量
 *
 * Interface ArrayAndJsonAble
 * @package Commune\Support
 */
interface ArrayAndJsonAble extends \JsonSerializable
{
    const PRETTY_JSON = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0) : string;

    /**
     * @return string
     */
    public function toPrettyJson() : string;

    /**
     * @return string
     */
    public function __toString();
}