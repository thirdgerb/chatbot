<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\RoutesMap;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * 单轮对话结束时, 对下一轮对话进行主动响应的路由图.
 * 上下文会根据路由的情况, 选择进入指定的 ucl 进行响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string[] $watching            监视类路由. 会触发 Watch 事件
 * [ string $ucl ]
 *
 * @property-read string[] $stageRoutes         分支路由, 会触发 Staging 事件
 * [ string $stageName ]
 *
 * @property-read string[] $contextRoutes       意图路由, 会触发 Intend 事件.
 *
 * @property-read string $heed                  默认的 Heed 对应的 ucl
 *
 * @property-read string[] $wake                唤醒路由, 会触发 Wake 事件
 * [ string $ucl => array $stages]
 *
 * @property-read string[] $restore             复活路由, 会触发 Rebirth 事件.
 * [ string $ucl => array $stages]
 *
 */
class IRoutesMap implements RoutesMap
{
    use ArrayAbleToJson;

    protected $_watching = [];

    protected $_stageRoutes = [];

    protected $_contextRoutes = [];

    protected $_heed = '';

    protected $_wake = [];

    protected $_restore = [];

    public function __construct(Process $process)
    {
        $waiter = $process->waiter;
        if (isset($waiter)) {
            $this->_heed = $waiter->await;
            $this->_stageRoutes = $waiter->stageRoutes;
            $this->_contextRoutes = $waiter->contextRoutes;
        }

        $this->_watching = array_keys($process->watching);

        foreach ($process->sleeping as $ucl => $stages) {
            if (!empty($stages)) {
                $this->_wake[$ucl] = $stages;
            }
        }

        foreach ($process->dying as $ucl => $stages) {
            if (!empty($stages)) {
                $this->_restore[$ucl] = $stages;
            }
        }

    }

    public function toArray(): array
    {
        $data = get_object_vars($this);
        $result = [];
        foreach($data as $key => $val) {
            $result[substr($key,1)] = $val;
        }
        return $result;
    }


    public function __get($name)
    {
        if (property_exists($this, $p = "_$name")) {
            return $this->{$p};
        }
        return null;
    }

}