<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Context;

use Commune\Blueprint\Ghost\Callables\DialogicService;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\ACodeContext;
use Commune\Message\Host\SystemInt\DialogForbidInt;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 用于子进程执行任务 Context.
 * 它最大的好处是, 可以给 Service 使用 Context - Cloner - Dialog 的各种组件.
 *
 * @property-read string $service   AsyncService 的类名
 *
 * @property array $payload    调用该 Service 时传入的参数.
 */
class AsyncServiceContext extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 1,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [
                'service',
            ],

            // memory 记忆体的默认值.
            'memoryAttrs' => [
                'payload' => [],
            ],
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate([$this, 'callService'])
            ->onEventExcept(
                $stage->dialog->quit(),
                Dialog::ACTIVATE
            );

    }

    public function callService(Dialog $dialog) : Operator
    {
        $service = $this->service;
        if (!is_a($service, DialogicService::class, TRUE)) {

            return $dialog
                ->send()
                ->error("invalid service class name $service")
                ->error("mission incomplete.")
                ->over()
                ->quit();
        }
        /**
         * @var DialogicService $service
         */
        $service = $dialog->container()->make($service);

        $auth = $service->auth();
        $authority = $dialog->cloner->auth;
        foreach ($auth as $ability) {
            if (!$authority->allow($ability)) {
                $forbidden = DialogForbidInt::instance($service->getId(), $ability);
                $dialog->send()
                    ->message($forbidden)
                    ->over()
                    ->quit();
            }
        }

        $service($this->payload, $dialog->send(true));
        return $dialog->quit();
    }
}