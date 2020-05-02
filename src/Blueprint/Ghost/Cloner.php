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

use Commune\Blueprint\Ghost;
use Commune\Contracts\Cache;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Auth\Authority;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Support\Option\OptRegistry;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Snapshot\Task;


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
 * # 对话模块
 * @property-read Convo $convo                      对话模块
 *
 * # 作用域
 * @property-read GhostConfig $config               机器人配置
 *
 * # Session
 * @property-read Cloner\ClonerLogger $logger         日志
 * @property-read Cloner\ClonerStorage $storage       Session Storage
 *
 * # 容器
 * @property-read ReqContainer $container           容器
 *
 * # 请求相关
 * @property-read GhostInput $ghostInput            输入
 * @property-read Cloner\ClonerScene $scene         场景信息
 * @property-read Cloner\ClonerScope $scope         当前分身的维度.
 * @property-read Ghost\Routing\Matcher
 *
 * # 功能组件
 * @property-read Cache $cache                      公共缓存
 * @property-read Authority $auth                   授权模块
 *
 * # 复杂对话逻辑组件
 *
 * @property-read Ghost\Mind\Mindset $mind          机器人的思维
 * @property-read Runtime $runtime                  机器人的运行状态
 *
 * # Host 组件
 * @property-read Ghost $ghost                      Ghost 本体
 * @property-read OptRegistry $registry                注册表
 *
 */
interface Cloner extends Session
{
    /**
     * @return string
     */
    public function getClonerId() : string;


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
     * @param array|null $queries
     * @return Context
     */
    public function findContext(string $contextName, array $queries = null) : Context;

    /**
     * 在当前上下文中创建一个 Task
     *
     * @param string $contextName
     * @param array|null $queries
     * @return Task
     */
    public function findTask(string $contextName, array $queries = null) : Task;

}