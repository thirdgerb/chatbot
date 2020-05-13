<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind;

use Commune\Ghost\Mind\IRegistries;
use Commune\Blueprint\Ghost\Mind\Mindset;
use Commune\Support\Registry\OptRegistry;
use Commune\Blueprint\Ghost\Mind\Regs;
use Commune\Ghost\Providers\MindCacheExpireOption;
use Commune\Blueprint\Ghost\Mind\Regs\DefRegistry;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMindset implements Mindset
{

    const REGISTRY_IMPL = [
        Regs\ContextReg::class => IRegistries\IContextReg::class,
        Regs\StageReg::class => IRegistries\IStageReg::class,
        Regs\IntentReg::class => IRegistries\IIntentReg::class,
        Regs\MemoryReg::class => IRegistries\IMemoryReg::class,
        Regs\EmotionReg::class => IRegistries\IEmotionReg::class,
        Regs\EntityReg::class => IRegistries\IEntityReg::class,
        Regs\SynonymReg::class => IRegistries\ISynonymReg::class,
    ];

    /**
     * @var OptRegistry
     */
    protected $optRegistry;

    /**
     * @var MindCacheExpireOption
     */
    protected $cacheExpire;

    /*---- cached ----*/

    protected $registries = [];

    /**
     * IMindset constructor.
     * @param OptRegistry $optRegistry
     * @param MindCacheExpireOption $option
     */
    public function __construct(OptRegistry $optRegistry, MindCacheExpireOption $option)
    {
        $this->optRegistry = $optRegistry;
        $this->cacheExpire = $option;
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

    public function initContexts(): void
    {
        $contextReg = $this->contextReg();
        foreach($contextReg->each() as $def) {
            $contextReg->registerDef($def);
        }
    }


    /*---- registries ----*/

    protected function getReg(string $type, int $cacheExpire) : DefRegistry
    {
        if (isset($this->registries[$type])) {
            return $this->registries[$type];
        }

        $impl = static::REGISTRY_IMPL[$type];

        return new $impl(
            $this,
            $this->optRegistry,
            $cacheExpire
        );
    }


    public function contextReg(): DefRegistry
    {
        return $this->getReg(
            Regs\ContextReg::class,
            $this->cacheExpire->context
        );
    }

    public function intentReg(): DefRegistry
    {
        return $this->getReg(
            Regs\IntentReg::class,
            $this->cacheExpire->intent
        );
    }

    public function stageReg(): DefRegistry
    {
        return $this->getReg(
            Regs\StageReg::class,
            $this->cacheExpire->stage
        );
    }

    public function memoryReg(): DefRegistry
    {
        return $this->getReg(
            Regs\MemoryReg::class,
            $this->cacheExpire->memory
        );
    }

    public function entityReg(): DefRegistry
    {
        return $this->getReg(
            Regs\EntityReg::class,
            $this->cacheExpire->entity
        );
    }

    public function synonymReg(): DefRegistry
    {
        return $this->getReg(
            Regs\SynonymReg::class,
            $this->cacheExpire->synonym
        );
    }

    public function emotionReg(): DefRegistry
    {
        return $this->getReg(
            Regs\EmotionReg::class,
            $this->cacheExpire->emotion
        );
    }


    public function __destruct()
    {
        $this->registries = [];
        $this->optRegistry = null;
    }
}