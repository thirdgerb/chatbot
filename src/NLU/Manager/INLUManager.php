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

use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\NLU\NLUManager;
use Commune\Blueprint\NLU\NLUService;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Ghost\Predefined\Services\Payload\MindSavePayload;
use Commune\NLU\Services\NLUSaveMetaService;


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
     * @var array
     */
    protected $map;

    public function listService(string $serviceInterface): array
    {
        return $this->options;
    }

    public function getService(Cloner $cloner, string $serviceInterface): ? NLUService
    {
        $option = $this->findServiceOption($serviceInterface);
        if (empty($option)) {
            return null;
        }

        return $cloner->container->make($option->serviceAbstract);
    }

    public function findServiceOption(string $serviceInterface) : ? NLUServiceOption
    {
        $map = $this->getServiceMap();
        if (isset($map[$serviceInterface])) {
            return $map[$serviceInterface];
        }

        foreach ($map as $serviceAbstract => $option) {
            if (is_a($serviceAbstract, $serviceInterface, true)) {
                $this->map[$serviceInterface] = $option;
                return $option;
            }
        }

        return null;
    }

    protected function getServiceMap() : array
    {
        if (isset($this->map)) {
            return $this->map;
        }

        $this->map = [];
        foreach ($this->options as $option) {
            $abstract = $option->serviceAbstract;
            $this->map[$abstract] = $option;
        }

        return $this->map;
    }


    public function asyncSaveMeta(Cloner $cloner, DefMeta $meta): void
    {
        $protocal = new MindSavePayload([
            'metaName' => get_class($meta),
            'metaData' => $meta->toArray(),
            'force' => true
        ]);

        $cloner
            ->dispatcher
            ->asyncService(
                NLUSaveMetaService::class,
                $protocal->toArray()
            );
    }


    public function saveMeta(Cloner $cloner, DefMeta $meta): ? string
    {
        $registry = $cloner->mind->getRegistry($meta);
        $registry->registerDef($meta->toWrapper());

        foreach ($this->options as $option)  {
            $abstract = $option->serviceAbstract;
            $service = $this->getService($cloner, $abstract);
            $error = $service->saveMeta($cloner, $meta);
            if (isset($error)) {
                return $error;
            }
        }
        return null;
    }

    public function registerService(NLUServiceOption $option): void
    {
        $this->options[$option->getId()] = $option;

        // 插入一个排序一次.
        uasort(
            $this->options,
            function(NLUServiceOption $opt1, NLUServiceOption $opt2) {
                // 倒排, 优先级大的排前面.
                return $opt2->priority - $opt1->priority;
            }
        );
    }

    public function __destruct()
    {
        unset(
            $this->options,
            $this->map
        );
    }

}