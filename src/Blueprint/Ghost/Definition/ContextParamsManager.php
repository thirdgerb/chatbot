<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Definition;

use Illuminate\Support\Collection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextParamsManager
{
    /*------- parser -------*/

    public function parseQuery(array $query) : array;

    /*------- parameters -------*/

    public function hasLongTermParameter() : bool;

    public function hasSessionParameter() : bool;

    /**
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name) : bool;

    /**
     * @param string $name
     * @return ContextParameter
     */
    public function getParameter(string $name) : ContextParameter;

    /**
     * @return ContextParameter[]
     */
    public function getParameters() : array;

    /**
     * @return Collection of ContextParameter[]
     */
    public function getQueryParams() : Collection;

    /**
     * @return Collection of ContextParameter[]
     */
    public function getLongTermParams() : Collection;

    /**
     * @return Collection of ContextParameter[]
     */
    public function getShortTermParams() : Collection;

    /**
     * @return Collection of ContextParameters[]
     */
    public function getEntityParams() : Collection;

    /**
     * 过滤 Entity 的值. Entity 默认的每一项都是数组.
     * @param array $entities
     * @return array
     */
    public function parseIntentEntities(array $entities) : array;

    /**
     * 所有需要填满的属性, 不填满时, 要么拒绝对话, 要么启动一个多轮对话去检查.
     * @return string[]
     */
    public function getQueryNames() : array;

    /**
     * Context 的默认值.
     * @return array
     */
    public function getDefaultValues() : array;


}