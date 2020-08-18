<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Manager;

use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\NLU\NLUManager;
use Commune\Blueprint\NLU\NLUService;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Components\Predefined\Services\MindsetSaveService;


/**
 * NLU 的管理器.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class INLUManager implements NLUManager
{
    /**
     * @var NLUServiceOption[]
     */
    protected $options = [];

    /**
     * @var ProcContainer
     */
    protected $container;

    protected $map;

    public function listService(string $serviceInterface): array
    {
        return $this->options;
    }


    public function getService(
        string $serviceInterface,
        string $id = null
    ): NLUService
    {
        if (!isset($this->map[$serviceInterface])) {
            $this->map[$serviceInterface] = $this->makeMap($serviceInterface);
        }

        if (isset($id)) {
            return $this->map[$serviceInterface][$id];
        } else {
            $map = $this->map[$serviceInterface];
            return current($map);
        }
    }

    public function hasService(
        string $serviceInterface,
        string $id = null
    ): bool
    {
        if (!isset($this->map[$serviceInterface])) {
            $this->map[$serviceInterface] = $this->makeMap($serviceInterface);
        }

        if (isset($id)) {
            return isset($this->map[$serviceInterface][$id]);
        } else {
            $map = $this->map[$serviceInterface];
            return count($map) > 0;
        }
    }

    protected function makeMap(string $serviceInterface) : array
    {
        $map = [];
        foreach ($this->options as $option) {
            if (is_a($option->serviceInterface, $serviceInterface, true )) {
                $map[$option->id] = $option;
            }
        }
        return $map;
    }

    public function save(Cloner $cloner, DefMeta $meta): array
    {
        $ran = [];
        $dispatcher = $cloner->dispatcher;
        $dispatcher->asyncService(
            MindsetSaveService::class,
            [
                'metaName' => get_class($meta),
                'metaData' => $meta->toArray(),
                'force' => true
            ]
        );

        $ran[] = Mindset::class;
        foreach ($this->options as $option)  {
            foreach ($option->listening as $event) {
                if (is_a($meta, $event, true)) {
                    $service = $this->getService(
                        $option->serviceInterface,
                        $option->serviceAbstract
                    );

                    $service->saveMeta($meta);
                    $ran[] = $option->id;
                }
            }
        }
        return $ran;
    }

    public function registerService(NLUServiceOption $option): void
    {
        $this->options[$option->getId()] = $option;
    }

    public function __destruct()
    {
        unset(
            $this->options,
            $this->map
        );
    }

}