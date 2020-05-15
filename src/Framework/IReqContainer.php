<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework;

use Commune\Container\ContainerContract;
use Commune\Container\RecursiveContainer;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;
use Commune\Blueprint\Framework\ReqContainer;


/**
 * 请求级容器的实例, 用 trait 来实现, 以方便 shell, ghost 的容器不相互冲突.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 所属需要实现以下两个 interface
 * @mixin ReqContainer
 * @mixin Spied
 */
class IReqContainer implements ReqContainer
{
    use RecursiveContainer, SpyTrait;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var bool
     */
    protected $booted = false;

    public function __construct(ContainerContract $parentContainer, string $id = null)
    {
        $this->parentContainer = $parentContainer;
        $this->uuid = $id ?? static::class;
    }

    /**
     * @return ContainerContract
     */
    public function getProcessContainer() : ContainerContract
    {
        return $this->parentContainer;
    }


    public function isInstanced(): bool
    {
        return $this->uuid !== static::class;
    }

    public function newInstance(string $id, ContainerContract $procContainer): ReqContainer
    {
        $container = new static($procContainer);
        $container->uuid = $id;
        static::addRunningTrace($id, $id);
        return $container;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function booted(): void
    {
        $this->booted = true;
    }

    public function destroy(): void
    {
        // 清空自己所有的实例, 但不清空父类和绑定.
        // 避免相互持有导致问题.
        $this->flushInstance();
    }


    public function __destruct()
    {
        // 回收 ID
        static::removeRunningTrace($this->uuid);
    }

}