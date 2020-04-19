<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype;

use Commune\Container\ContainerContract;
use Commune\Container\RecursiveContainer;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * 请求级容器的实例, 用 trait 来实现, 以方便 shell, ghost 的容器不相互冲突.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 所属需要实现以下两个 interface
 * @mixin ReqContainer
 * @mixin Spied
 */
trait TReqContainer
{
    use RecursiveContainer, SpyTrait;

    protected $id = __CLASS__;

    /**
     * @return ContainerContract
     */
    public function getProcessContainer() : ContainerContract
    {
        return $this->parentContainer;
    }


    public function isInstanced(): bool
    {
        return $this->id !== __CLASS__;
    }

    public function newInstance(string $id, ContainerContract $procContainer): ReqContainer
    {
        $container = new static($procContainer);
        $container->id = $id;
        static::addRunningTrace($id, $id);
        return $container;
    }

    public function finish(): void
    {
        // 清空自己所有的实例, 但不清空父类和绑定.
        // 避免相互持有导致问题.
        $this->flushInstance();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __destruct()
    {
        // 回收 ID
        static::removeRunningTrace($this->id);
    }

}