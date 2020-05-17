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
use Commune\Blueprint\Ghost;
use Commune\Contracts\Cache;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Auth\Authority;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\GhostMsg;
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
 * @property-read Ghost\Tools\Matcher $matcher      全局的匹配单元
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
    /**
     * @return string
     */
    public function getClonerId() : string;


    /*----- 运行对话管理逻辑 -----*/

    /**
     * 获取上下文相关的 Query 变量.
     * @param string $contextName
     * @param array|null $query
     * @return array
     * @throws Ghost\Exceptions\DefNotDefinedException
     */
    public function getContextualQuery(string $contextName, array $query = null) : array;

    /**
     * 获取 Context 上下文相关的 entity 值.
     * @param string $contextName
     * @return array
     * @throws Ghost\Exceptions\DefNotDefinedException
     */
    public function getContextualEntities(string $contextName) : array;

    /**
     * @param Ucl $ucl
     * @return Context
     * @throws Ghost\Exceptions\DefNotDefinedException
     */
    public function getContext(Ucl $ucl) : Context;

    /**
     * @param Dialog|null $dialog
     * @return bool
     */
    public function runDialogManager(Dialog $dialog = null) : bool;

    /*----- 手动输出 -----*/

    /**
     * 静音开关, 打开静音开关, 接下来的 output 都不会真正接收.
     * @param bool $silent
     */
    public function silence(bool $silent = true) : void;

    /**
     * 同步输出一个消息.
     * @param GhostMsg $output
     * @param GhostMsg[] $outputs
     */
    public function output(GhostMsg $output, GhostMsg ...$outputs) : void;

    /**
     * 获得所有的输出消息.
     * @return GhostMsg[]
     */
    public function getOutputs() : array;

    /**
     * 提交一个异步输入消息.
     * @param GhostInput $ghostInput
     */
    public function asyncInput(GhostInput $ghostInput) : void;

    /**
     * 获取异步的输入消息
     * @return GhostInput[]
     */
    public function getAsyncInput() : array;


}