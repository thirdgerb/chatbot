<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Host;
use Commune\Blueprint\Messenger;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Auth\Authority;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Support\Option\Registry;
use Commune\Contracts\Cache;
use Commune\Blueprint\Ghost\Operator\Operator;


/**
 *
 * Ghost 的分身, 多轮对话的管理器.
 * 核心方法:
 *
 * $cloner->runDialogManage($operator = null);
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 作用域
 * @property-read CloneScope $scope                 当前分身的维度.
 * @property-read GhostConfig $config               机器人配置
 *
 * # 对话模块
 * @property-read Convo $convo                      对话模块
 *
 * # 容器
 * @property-read ReqContainer $container           容器
 *
 * # 请求相关
 * @property-read GhostInput $ghostInput
 * @property-read Convo\Scene $scene
 * @property-read Convo\ConvoRouter $router
 * @property-read Convo\ConvoLogger $logger         日志
 *
 * # 功能组件
 * @property-read Cache $cache                      公共缓存
 * @property-read Messenger $messenger              消息管理器.
 * @property-read Authority $auth                   授权模块
 *
 * # 复杂对话逻辑组件
 *
 * @property-read Ghost\Mind\Mindset $mind          机器人的思维
 * @property-read Ghost\Memory\Memory $memory       机器人的记忆
 * @property-read Runtime $runtime                  机器人的运行状态
 *
 * # Host 组件
 * @property-read Host $host                        Host 本体
 * @property-read Ghost $ghost                      Ghost 本体
 * @property-read Registry $registry                注册表
 * @property-read Platform $platform                当前所在平台
 * @property-read Platform\Server $server           当前所在 Server 实例
 *
 */
interface Cloner extends Session
{
    /*----- 锁 -----*/

    /**
     * 锁定一个机器人的分身. 禁止通讯.
     *
     * @param int $second
     * @return bool
     */
    public function lock(int $second) : bool;

    /**
     * @return bool
     */
    public function isLocked() : bool;

    /**
     * 解锁一个机器人的分身. 允许通讯.
     * @return bool
     */
    public function unlock() : bool;

    /*----- 运行对话管理逻辑 -----*/

    /**
     * 运行机器人的多轮对话逻辑.
     *
     * @param Operator|null $operator       指定一个启动算子.
     * @return bool                         表示对话是否成功响应了输入消息.
     */
    public function runDialogManager(Operator $operator = null) : bool;

    /*----- 获取 Context -----*/

    /**
     * 在当前的上下文中创建一个 Context
     *
     * @param string $contextName
     * @param array|null $entities
     * @return Context
     */
    public function newContext(string $contextName, array $entities = null) : Context;
}