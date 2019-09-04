<?php


namespace Commune\Chatbot\OOHost\Context;
use Commune\Chatbot\OOHost\Command\CommandDefinition;


/**
 * 定义 context 的默认属性. 这些属性通过多轮对话补完之后, context 才会进入 __onStart
 * 这些属性可以通过 "@property" 注解的方式在context类上定义, 让IDE可读性更好.
 *
 * 所有属性都有一组默认的 checkpoint, 来实现多轮对话.
 * 可以通过定义 __on{ucfirst($name)} 方法, 来主动设计该轮对话.
 *
 * @property Definition $definition
 */
interface Depending
{

    /**
     * 依赖一个memory实例.
     * 会读取当前类的 docComment
     * 把注解 "@property [typehint] $name [description] "
     * 当成一个问答式的依赖.
     *
     * @param array|null $names 为null时, 会把所有注解都当成depending
     * @return Depending
     */
    public function onAnnotations(array $names = null) : Depending;


    /**
     * 把一个命令的参数作为entity的定义
     *
     * @param CommandDefinition $def
     * @return Depending
     */
    public function onCommand(CommandDefinition $def) : Depending;

    /**
     * 将一个命令的字符串定义转化为 CommandDefinition
     * 然后基于命令来注册 entity
     *
     * @param string $signature
     * @return Depending
     */
     public function onSignature(string $signature) : Depending;

    /**
     * 依赖一个答案 ( answer )
     * 会提出问题 AskVerbose('ask.default', [ '%name', '%desc%', '%default%'])
     * 将答案 ($answerMessage->toResult() ) 作为当前context 的属性.
     *
     * @param string $name
     * @param string $question
     * @param null $default
     * @return Depending
     */
    public function on(
        string $name,
        string $question = '',
        $default = null
    ) : Depending;

    /**
     * 依赖一个context 实例.
     * 当依赖的 context fulfill 的时候, 会回调过来.
     *
     * @param string $name
     * @param string $contextName
     * @return Depending
     */
    public function onContext(string $name, string $contextName) : Depending;

    /**
     * 依赖一个memory实例.
     * 如果 memory 实例 isPrepared === false,
     * 则会进入多轮对话补完该 memory
     *
     * @param string $name
     * @param string $memoryName
     * @return Depending
     */
    public function onMemory(string $name, string $memoryName) : Depending;

    /**
     * 依赖一个memory 的一个值.
     *
     * @param string $name
     * @param string $memoryName
     * @param string $memoryKey
     * @return Depending
     */
    public function onMemoryVal(string $name, string $memoryName, string $memoryKey) : Depending;


    /**
     * 手动添加一个Entity 对象.
     * 来定义一个依赖.
     *
     * @param Entity $entity
     * @return Depending
     */
    public function onEntity(Entity $entity) : Depending;
}