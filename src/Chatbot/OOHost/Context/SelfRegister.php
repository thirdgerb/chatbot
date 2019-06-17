<?php


namespace Commune\Chatbot\OOHost\Context;


/**
 * 可以静态地注册自身的Definition
 */
interface SelfRegister
{
    const REGISTER_METHOD = 'registerSelfDefinition';

    public static function registerSelfDefinition() : void;
}