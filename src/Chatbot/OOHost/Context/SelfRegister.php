<?php


namespace Commune\Chatbot\OOHost\Context;
use Commune\Container\ContainerContract;


/**
 * Context 可以用静态方法注册自身的 Definition 到 Registrar
 */
interface SelfRegister
{
    const REGISTER_METHOD = 'registerSelfDefinition';

    public static function registerSelfDefinition(ContainerContract $processContainer) : void;
}