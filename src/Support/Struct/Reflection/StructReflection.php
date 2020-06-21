<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct\Reflection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StructReflection
{
    /**
     * @return string
     */
    public function getStructName() : string;

    /**
     * 是否强类型校验.
     * @return bool
     */
    public function isStrict() : bool;

    /**
     * @return StructProperty[]
     */
    public function getDefinedPropertyMap() : array;

    /**
     * 是否定义了一个属性.
     *
     * @param string $name
     * @return bool
     */
    public function isPropertyDefined(string $name) : bool;

    /**
     * 获取一个属性的定义.
     * 不过这不是强约束, 可以不定义.
     *
     * @param string $name
     * @return StructProperty|null
     */
    public function getProperty(string $name) : ? StructProperty;

    /**
     * 允许定义 Property.
     * @param StructProperty $property
     */
    public function defineProperty(StructProperty $property) : void;

    /**
     * @return string[]
     */
    public function getRelationNames() : array;

    /**
     * 校验数据, 并返回第一个错误信息.
     *
     * @param array $data
     * @return null|string
     */
    public function validate(array $data) : ? string;

}

