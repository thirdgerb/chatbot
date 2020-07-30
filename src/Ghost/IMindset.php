<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Blueprint\Ghost\Mindset;
use Commune\Support\Registry\OptRegistry;
use Commune\Blueprint\Ghost\MindReg;
use Commune\Blueprint\Ghost\MindReg\DefRegistry;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMindset implements Mindset
{

    const REGISTRY_IMPL = [
        MindReg\ContextReg::class => IMindReg\IContextReg::class,
        MindReg\StageReg::class => IMindReg\IStageReg::class,
        MindReg\IntentReg::class => IMindReg\IIntentReg::class,
        MindReg\MemoryReg::class => IMindReg\IMemoryReg::class,
        MindReg\EmotionReg::class => IMindReg\IEmotionReg::class,
        MindReg\EntityReg::class => IMindReg\IEntityReg::class,
        MindReg\SynonymReg::class => IMindReg\ISynonymReg::class,
    ];

    /**
     * @var OptRegistry
     */
    protected $optRegistry;

    /**
     * @var int
     */
    protected $cacheExpire;

    /*---- cached ----*/

    protected $registries = [];

    /**
     * IMindset constructor.
     * @param OptRegistry $optRegistry
     * @param int $cacheExpire
     */
    public function __construct(OptRegistry $optRegistry, int $cacheExpire)
    {
        $this->optRegistry = $optRegistry;
        $this->cacheExpire = $cacheExpire;
    }

    public function reset(): void
    {
        $this->contextReg()->reset();
        $this->intentReg()->reset();
        $this->stageReg()->reset();
        $this->memoryReg()->reset();
        $this->entityReg()->reset();
        $this->synonymReg()->reset();
        $this->emotionReg()->reset();
    }


    public function reload(): void
    {
        $this->contextReg()->flushCache();
        $this->intentReg()->flushCache();
        $this->stageReg()->flushCache();
        $this->memoryReg()->flushCache();
        $this->entityReg()->flushCache();
        $this->synonymReg()->flushCache();
        $this->emotionReg()->flushCache();
    }

    /*---- registries ----*/

    protected function getReg(string $type, int $cacheExpire) : DefRegistry
    {
        if (isset($this->registries[$type])) {
            return $this->registries[$type];
        }

        $impl = static::REGISTRY_IMPL[$type];
        return $this->registries[$type] = new $impl(
            $this,
            $this->optRegistry,
            $cacheExpire
        );
    }


    public function contextReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\ContextReg::class,
            $this->cacheExpire
        );
    }

    public function intentReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\IntentReg::class,
            $this->cacheExpire
        );
    }

    public function stageReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\StageReg::class,
            $this->cacheExpire
        );
    }

    public function memoryReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\MemoryReg::class,
            $this->cacheExpire
        );
    }

    public function entityReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\EntityReg::class,
            $this->cacheExpire
        );
    }

    public function synonymReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\SynonymReg::class,
            $this->cacheExpire
        );
    }

    public function emotionReg(): DefRegistry
    {
        return $this->getReg(
           MindReg\EmotionReg::class,
            $this->cacheExpire
        );
    }


    public function __destruct()
    {
        $this->registries = [];
        // 有一些 unset 是排查问题时碰到的.
        unset($this->optRegistry);
    }
}