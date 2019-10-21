<?php


namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\Blueprint\Application;

interface ParentContextRegistrar extends ContextRegistrar
{
    /**
     * 获取App
     *
     * @return Application
     */
    public function getChatApp() : Application;

    /**
     * 是否已经注册了其它的子容器.
     * @param string $registrarId
     * @return bool
     */
    public function hasSubRegistrar(string $registrarId) : bool;

    /**
     * @param string $id
     */
    public function registerSubRegistrar(string $id) : void;

    /**
     * 获取子容器
     * @param bool $recursive
     * @return \Generator ContextRegistrar[]
     */
    public function eachSubRegistrar($recursive = true) : \Generator;

    /**
     * @param string $id
     * @param bool $recursive
     * @return ContextRegistrar|null
     */
    public function getSubRegistrar(string $id, bool $recursive =false) : ? ContextRegistrar;

    /**
     * @return ContextRegistrar
     */
    public function getDefault() : ContextRegistrar;


}