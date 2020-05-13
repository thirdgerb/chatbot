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

use Commune\Ghost\Mind\Metas;
use Commune\Ghost\Mind\IRegistries;
use Commune\Blueprint\Ghost\Mind\Registries;
use Commune\Blueprint\Ghost\Mind\Mindset;
use Commune\Support\Registry\OptRegistry;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMindset implements Mindset
{

    const REGISTRY_IMPL = [
        Registries\ContextReg::class => IRegistries\IContextReg::class,
        Registries\EntityReg::class => IRegistries\IEntityReg::class,

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

    /*---- registries ----*/

    protected function getReg(string $type) : Registries\DefRegistry
    {
        if (isset($this->registries[$type])) {
            return $this->registries[$type];
        }

        $impl = static::REGISTRY_IMPL[$type];

        return new $impl(
            $this,
            $this->optRegistry,
            $this->cacheExpire
        );
    }


    public function contextReg(): Registries\ContextReg
    {
        return $this->getReg(Registries\ContextReg::class);
    }

    public function intentReg(): Registries\IntentReg
    {
        // TODO: Implement intentReg() method.
    }

    public function stageReg(): Registries\StageReg
    {
        // TODO: Implement stageReg() method.
    }

    public function memoryReg(): Registries\MemoryReg
    {
        // TODO: Implement memoryReg() method.
    }

    public function entityReg(): Registries\EntityReg
    {
        return $this->getReg(Registries\EntityReg::class);
    }

    public function synonymReg(): Registries\SynonymReg
    {
        return $this->getReg(Registries\SynonymReg::class);
    }


    public function __destruct()
    {
        $this->registries = [];
        $this->optRegistry = null;
    }
}