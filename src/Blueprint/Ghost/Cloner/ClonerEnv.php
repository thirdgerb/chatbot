<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;


/**
 * 分身当前请求的环境变量.
 * Env 通过 GhostRequest 传递, 但不约束
 *
 * 从这些环境变量中, 我们可以获取请求的更多信息. 是一种弱协议策略.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 以下是默认的环境变量.
 *
 * @property-read int $userLevel            用户等级信息
 * @property-read array $userInfo           用户更多信息. 视客户端决定是否存在.
 * 
 * @property-read string|null $lang         语言类型
 * @property-read array $clientInfo         更详细的客户端场景数据. 
 *
 * @property-read string|null $botId        机器人Id
 * @property-read string|null $botName      机器人名称
 * @property-read array $botInfo            机器人更多参数
 *
 */
interface ClonerEnv extends \ArrayAccess
{
    // 更多用户信息
    const USER_LEVEL_KEY = 'userLevel';
    const USER_INFO_KEY = 'userInfo';
    
    // 环境语言
    const LANG_LOCALE_KEY = 'lang';
    const CLIENT_INFO = 'clientInfo';
    
    // 更多机器人信息
    const BOT_NAME_KEY = 'botName';
    const BOT_ID_KEY = 'botId';
    const BOT_INFO_KEY = 'botInfo';

    /**
     * 传入进来的环境变量.
     * @return array
     */
    public function getData() : array;
}