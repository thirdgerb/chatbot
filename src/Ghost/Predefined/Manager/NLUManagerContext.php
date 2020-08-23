<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Manager;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\NLU\NLUService;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Ghost\Context\ACodeContext;

/**
 * 所有的中文描述未来再改吧....
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title NLU 服务管理
 * @desc 管理 NLU 服务模块
 *
 *
 * @property-read NLUServiceOption[] $serviceManagers    服务管理对话
 */
class NLUManagerContext extends ACodeContext
{
    /**
     * @var NLUServiceOption|null
     */
    protected $_services;

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'priority' => 1,
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {
            return $dialog
                ->send()
                ->info('管理 NLU 的各种服务')
                ->over()
                ->goStage('services');
        });
    }

    public function __on_services(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function (Dialog $dialog) {

                $services = $this->serviceManagers;
                $map = [];
                foreach ($services as $option) {
                    $desc = $option->desc;
                    $key = "|$desc";
                    $ucl = Ucl::decode($option->managerUcl);
                    $map[$key] = $ucl;
                }

                return $dialog
                    ->await()
                    ->askChoose(
                        "请选择要管理的服务:",
                        $services
                    );

            });


    }

    public function __get_serviceManagers() : array
    {
        if (!isset($this->_services)) {
            $this->_services = [];
            $options = $this
                ->getCloner()
                ->nlu
                ->listService(NLUService::class);

            foreach ($options as $option) {
                $manager = $option->managerUcl;
                if (!empty($manager)) {
                    $this->_services[] = $option;
                }
            }
        }

        return $this->_services;
    }
}