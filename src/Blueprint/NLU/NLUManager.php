<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;

use Commune\Blueprint\Ghost\Cloner;
use Commune\NLU\Exceptions\NLUServiceNotFoundException;

/**
 * 所有的 NLU 模块的公共管理中心.
 * 各种 nlu 服务都应该注册进来, 并进行统一的管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface NLUManager
{

    /**
     * 列出已有的服务.
     * @param string $serviceInterface
     * @return NLUServiceOption[]
     */
    public function listService(string $serviceInterface) : array;

    /**
     * 获取一个服务. 如果服务不存在, 抛出异常.
     * @param Cloner $cloner
     * @param string $serviceInterface
     * @return NLUService
     * @throws NLUServiceNotFoundException
     */
    public function getService(
        Cloner $cloner,
        string $serviceInterface
    ) : ? NLUService;

    /**
     * 注册一个服务
     * @param NLUServiceOption $option
     */
    public function registerService(NLUServiceOption $option) : void;

    /*------- learn ------*/
//
//    /**
//     * 保存一个 meta 数据. 只应该保存和当前功能相关的.
//     * @param DefMeta $meta
//     * @return string|null   error message
//     */
//    public function saveMeta(DefMeta $meta) : ? string;
//
//
//    const LEARN_INTENT = 1;
//    const LEARN_ENTITY = 2;
//    const LEARN_SYNONYM = 3;
//    const LEARN_CHAT = 4;
//
//    public function learn(
//        int $mode,
//        string $name,
//        string $value
//    ) : void;

}