<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Services\Context;

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
 * @title 子进程执行一次性任务 Context.
 *
 * 它最大的好处是, 可以给 Service 使用 Context - Cloner - Dialog 的各种组件.
 *
 * @property-read string $service   AsyncService 的类名
 * @property-read string $method    调用的 AsyncService  的方法.
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
                'method',
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
        $serviceName = $this->service;

        if (!is_a($serviceName, DialogicService::class, TRUE)) {
            return $dialog
                ->send()
                ->error("invalid service class name $serviceName")
                ->error("mission incomplete.")
                ->over()
                ->quit();
        }
        try {
            /**
             * @var DialogicService $service
             */
            $service = $dialog->container()->make($serviceName);

            $auth = $service->auth();
            $authority = $dialog->cloner->auth;
            foreach ($auth as $ability) {
                if (!$authority->allow($ability)) {
                    $forbidden = DialogForbidInt::instance($serviceName, $ability);
                    $dialog->send()
                        ->message($forbidden)
                        ->over()
                        ->quit();
                }
            }

            if ($this->method === '__invoke') {
                $operator = $service($dialog, $this->payload);
            } else {
                $operator = $dialog->container()->call(
                    [$service, $this->method],
                    [
                        'payload' => $this->payload,
                    ]
                );
            }
            return $operator ?? $dialog->quit();

        } catch (\Throwable $e) {
            return $dialog
                ->send()
                ->error(get_class($e) . ':' . $e->getMessage())
                ->over()
                ->quit();
        }
    }
}