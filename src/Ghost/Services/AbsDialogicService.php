<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Services;

use Commune\Blueprint\Ghost\Callables\DialogicService;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Tools\Deliver;


/**
 * 在对话中执行的任务.
 * 通过 Cloner->$dispatcher->asyncService($serviceName, array $params) 方式运行.
 *
 * @see \Commune\Blueprint\Ghost\Cloner\ClonerDispatcher
 * @see \Commune\Blueprint\Ghost\Callables\DialogicService
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialogicService implements DialogicService
{
    /**
     * 定义需要校验的权限.
     *
     * @see \Commune\Blueprint\Framework\Auth\Policy
     * @var array
     */
    protected $auth = [];

    /**
     * 定义任务的逻辑.
     *
     * __construct 方法可以用来依赖注入.
     *
     * @param array $payload
     * @param Deliver $deliver
     */
    abstract public function handle(array $payload, Deliver $deliver): void;

    /**
     * 权限校验
     * @return array
     */
    public function auth(): array
    {
        return $this->auth;
    }

    /**
     * 默认的执行方法.
     *
     * @param Dialog $dialog
     * @param array $payload
     * @return Operator|null
     * @throws \Throwable
     */
    public function __invoke(Dialog $dialog, array $payload): ? Operator
    {
        try {
            $this->handle($payload, $dialog->send(true));
            return null;

        } catch (\Throwable $e) {

            return $dialog
                ->send()
                ->error(
                    static::class . '::' . __FUNCTION__
                    . ' failed: '
                    . get_class($e)
                    . ':'
                    . $e->getMessage()
                )
                ->over()
                ->quit();
        }
    }


}