<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Session;

use Commune\Framework\Blueprint\Intercom\GhostOutput;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Ghost\Blueprint\Auth\Authority;
use Commune\Ghost\Blueprint\Chat\Chat;
use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Definition\Mindset;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\Memory\Memory;
use Commune\Ghost\Blueprint\Meta\MetaRegistrar;
use Commune\Ghost\Blueprint\Runtime\Runtime;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Framework\Blueprint\Intercom\ShellScope;
use Commune\Message\Blueprint\Message;
use Commune\Support\Babel\BabelResolver;
use SebastianBergmann\CodeCoverage\Driver\Driver;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * 以下内容可以依赖注入
 *
 * 请求
 *
 * @property-read GhtRequest $request               本轮对话的同步请求
 * @property-read GhtResponse $response             本轮对话的同步响应
 * @property-read ShellInput $incoming                输入的消息
 * @property-read Scene $scene                      场景信息
 * @property-read ShellScope $scope                      本轮对话的作用域
 * @property-read ReqContainer $container           请求级容器
 * @property-read Chat $chat
 *
 * 组件
 *
 * @property-read Ghost $ghost                      对话机器人的灵魂
 * @property-read Mindset $mind                     对话机器人的思维. 公共的
 * @property-read MetaRegistrar $metaReg            元数据的注册表
 * @property-read GhtSessionLogger $logger          请求级日志
 * @property-read Driver $driver                    Session 的驱动, 读写各种数据.
 * @property-read Cache $cache                      缓存
 * @property-read BabelResolver $babel
 * @property-read Messenger $messenger
 * @property-read Authority $authority
 * @property-read Memory $memory                    机器人的记忆
 * @property-read Speaker $output
 *
 */
interface GhtSession
{


    /**
     * @param ShellOutput[] $replies
     */
    public function output(array $replies) : void;

    /**
     * @return GhostOutput[]
     */
    public function getOutputs() : array;

    /**
     * @param string $policy
     * @return Message|null
     */
    public function allow(string $policy) : ? Message;

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;
}