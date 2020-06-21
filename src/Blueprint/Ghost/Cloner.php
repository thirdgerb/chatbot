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
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Framework\Auth\Authority;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Registry\OptRegistry;


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
 * @property-read GhostConfig $config               机器人配置
 *
 * # Session
 * @property-read Cloner\ClonerLogger $logger       日志
 * @property-read Cloner\ClonerStorage $storage     Session Storage
 *
 * # 容器
 * @property-read ReqContainer $container           容器
 *
 * # 请求相关
 * @property-read InputMsg $input                   输入
 * @property-read Comprehension $comprehension      理解模块
 * @property-read Cloner\ClonerScene $scene         场景信息
 * @property-read Cloner\ClonerScope $scope         当前分身的维度.
 * @property-read Ghost\Tools\Matcher $matcher      默认的匹配逻辑工具
 *
 * # 功能组件
 * @property-read Cache $cache                      公共缓存
 * @property-read Authority $auth                   授权模块
 *
 * # 复杂对话逻辑组件
 *
 * @property-read Ghost\Mindset $mind               机器人的思维
 * @property-read Runtime $runtime                  机器人的运行状态
 *
 * # Host 组件
 * @property-read Ghost $ghost                      Ghost 本体
 * @property-read OptRegistry $registry             配置注册表
 *
 */
interface Cloner extends Session
{

    /*----- status -----*/

    /**
     * 设置为无状态请求
     */
    public function noState() : void;

    /**
     * 是否是无状态的 session
     * @return bool
     */
    public function isStateless() : bool;

    /*----- conversation -----*/

    /**
     * @return string
     */
    public function getConversationId() : string;

    /**
     * 退出当前多轮对话.
     */
    public function endConversation() : void;

    /**
     * 当前多轮对话是否已经结束.
     * @return bool
     */
    public function isConversationEnd() : bool;

    /**
     * 多轮对话不记录状态 (主要是 Runtime), 但不影响 Session
     */
    public function noConversationState(): void;

    /*----- 手动输出 -----*/

    /**
     * 输出消息.
     * @param OutputMsg $output
     * @param OutputMsg[] $outputs
     */
    public function output(OutputMsg $output, OutputMsg ...$outputs) : void;

    /**
     * 获得所有的输出消息.
     * @return IntercomMsg[]
     */
    public function getOutputs() : array;

    /**
     * 提交一个异步输入消息.
     * @param InputMsg $input
     */
    public function asyncInput(InputMsg $input) : void;

    /**
     * 获取异步的输入消息
     * @return InputMsg[]
     */
    public function getAsyncInputs() : array;


    /*----- clone locker -----*/

    /**
     * 锁定一个 session 用于禁止通讯.
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

    /**
     * @param $name
     * @return bool
     */
    public function isSingletonInstanced($name) : bool;

}