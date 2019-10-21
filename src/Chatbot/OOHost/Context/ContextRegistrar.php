<?php


namespace Commune\Chatbot\OOHost\Context;
use Commune\Chatbot\Blueprint\Application;

/**
 * Context Definition 的仓库.
 * 保存上下文的定义.
 * Definition 是上下文所有 stage, entity 和其它操作方法(callable)的仓库.
 * 而 Registrar 在系统启动时, 往内存中保存这些定义, 并支持各种调用.
 *
 */
interface ContextRegistrar
{

    /**
     * 获取父容器
     * @return ContextRegistrar|null
     */
    public function getParent() : ? ContextRegistrar;

    /**
     * 容器的唯一ID. 在整个容器树中, 不应该有两个同名的容器,
     * 否则可能会隐藏各种难以理解的错误.
     *
     * @return string
     */
    public function getRegistrarId() : string;

    /**
     * 获取App
     *
     * @return Application
     */
    public function getChatApp() : Application;

    /**
     * 是否已经注册了其它的子容器.
     * @param string $registrarName
     * @return bool
     */
    public function hasSubRegistrar(string $registrarName) : bool;

    /**
     * 添加一个context 的子容器.
     *
     * 比如根容器是 contextRegistrar, 子容器有 intentRegistrar, memoryRegistrar, 从而构成容器树.
     *
     * 容器理论上可以在内存, 在数据表, 在缓存等各种地方存储 definition的数据.
     *
     * @param string $name
     * @param ContextRegistrar $registrar
     */
    public function addSubRegistrar(string $name, ContextRegistrar $registrar) : void;


    /**
     * 获取子容器
     * @param bool $recursive 是否递归地获取所有
     * @return ContextRegistrar[]
     */
    public function getSubRegistrars($recursive = true) : array;

    /**
     * 注册一个 context definition 通常是启动时注册到内存.
     * 也可以自己开发Registrar, 不需要在启动时注册.
     *
     * @param Definition $def
     * @param bool $force  override existing definition
     * @return bool
     */
    public function registerDef(Definition $def, bool $force = false)  : bool;

    public function hasDef(string $contextName) : bool;

    public function getDef(string $contextName) : ? Definition;

    /**
     * 检查 context name 是否合法.
     * 在系统中只有 字母, 数字, 下划线 和 . 是合法的.
     * 然而PHP的类名中 \ 会转换成 .
     *
     * 大小写不敏感.
     *
     * @param string $contextName
     * @return bool
     */
    public static function validateDefName(string $contextName) : bool;

    /**
     * @return Definition[]
     */
    public function eachDef() : \Generator;

    /**
     * 已注册的context 数量.
     * @return int
     */
    public function countDef() : int;

    /**
     * @param string $domain
     * @return string[]
     */
    public function getDefNamesByDomain(string $domain = '') : array;


    /**
     * 按 definition->getTags() 的tag 来反查.
     * @param string[] $tags
     * @return string[]
     */
    public function getDefNamesByTag(string ...$tags) : array;

    /**
     * 仍然使用了 placeholder 的context
     * @return string[]
     */
    public function getPlaceholderDefNames() : array;
}