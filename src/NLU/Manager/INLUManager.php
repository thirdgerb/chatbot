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

use Commune\Blueprint\NLU\NLUManager;
use Commune\Blueprint\NLU\NLUService;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Blueprint\Ghost\Cloner;


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

//
//
//    public function asyncSaveMeta(Cloner $cloner, DefMeta $meta): void
//    {
//        $protocal = new MindSavePayload([
//            get_class($meta),
//            $meta->toArray(),
//            true
//        ]);
//
//        $cloner
//            ->dispatcher
//            ->asyncService(
//                NLUSaveMetaService::class,
//                $protocal->toArray()
//            );
//    }
//
//
//    public function saveMeta(Cloner $cloner, DefMeta $meta): array
//    {
//        $ran = [];
//        $registry = $cloner->mind->getRegistry($meta);
//        $registry->registerDef($meta->toWrapper());
//        $ran[] = get_class($registry) . '::registerDef';
//
//        $ran[] = Mindset::class;
//        foreach ($this->options as $option)  {
//            foreach ($option->listening as $event) {
//                if (is_a($meta, $event, true)) {
//                    $abstract = $option->serviceAbstract;
//                    $service = $this->getService($cloner, $abstract);
//                    $service->saveMeta($meta);
//                    $ran[] = "$abstract::saveMeta";
//                }
//            }
//        }
//        return $ran;
//    }

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