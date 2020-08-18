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
use Commune\Blueprint\Ghost\MindMeta\DefMeta;

/**
 * 所有的 NLU 模块的公共管理中心.
 * 各种 nlu 服务都应该注册进来, 并进行统一的管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface NLUManager
{
    /*------- full text search ------*/

    /**
     * @param string $serviceInterface
     * @return NLUServiceOption[]
     */
    public function listService(string $serviceInterface) : array;

    /**
     * @param string $serviceInterface
     * @param string|null $id
     * @return NLUService
     */
    public function getService(
        string $serviceInterface,
        string $id = null
    ) : NLUService;

    public function hasService(
        string $serviceInterface,
        string $id = null
    ) : bool;

    /**
     * @param Cloner $cloner
     * @param DefMeta $meta
     * @return string[] 执行过 save 的模块名
     */
    public function save(Cloner $cloner, DefMeta $meta) : array;

    public function registerService(NLUServiceOption $option) : void;


}