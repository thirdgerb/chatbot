<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Convo\QA;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Contracts\Trans\SelfTranslatable;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\HostMsg\Tags\Conversational;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface QuestionMsg extends VerbalMsg, Conversational, SelfTranslatable
{
    const MATCH_INDEX = 1 << 1;
    const MATCH_SUGGESTION = 1 << 2;
    const MATCH_INTENT = 1 << 3;
    const MATCH_ANY = 1 << 4;

    /**
     * @return string
     */
    public function getQuery() : string;

    /**
     * @return string[]
     */
    public function getSuggestions() : array;

    /**
     * @param string $choice
     */
    public function addDefault(string $choice) : void;

    /**
     * @param string $suggestion
     * @param string|int|null $index
     * @param Ucl|null $ucl
     * @return mixed                返回索引.
     */
    public function addSuggestion(string $suggestion, $index = null, Ucl $ucl = null);

    /**
     * @return Ucl[]
     */
    public function getRoutes() : array;

    /**
     * 添加 slots 参数, 方便做转义.
     * @param array $slots
     * @return QuestionMsg
     */
    public function withSlots(array $slots) : QuestionMsg;


    /**
     * 前置分析. 在没有意图信息的情况下检查对话
     * 如果有 route 结果, 直接重定向
     *
     * @param Cloner $cloner
     * @return AnswerMsg
     */
    public function parse(Cloner $cloner) : ? AnswerMsg;

    /**
     * 后置分析, 在已经经过 comprehend pipe 后
     * 检查是否有意图能够响应当前选项.
     *
     * @param Cloner $cloner
     * @return AnswerMsg|null
     */
    public function match(Cloner $cloner) : ? AnswerMsg;

    /*------ 设置匹配模式 ------*/

    public function isMatchMode(int $mode) : bool;

    public function setMatchMode(int $mode) : void;

    public function withoutMatchMode(int $mode) : void;

    public function getMatchMode() : int;
}