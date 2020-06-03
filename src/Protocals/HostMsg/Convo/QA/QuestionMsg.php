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
use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface QuestionMsg extends VerbalMsg
{
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
     */
    public function addSuggestion(string $suggestion, $index = null, Ucl $ucl = null) : void;

    /**
     * @return Ucl[]
     */
    public function getRoutes() : array;

    /**
     * @param Cloner $cloner
     * @return AnswerMsg
     */
    public function parse(Cloner $cloner) : ? AnswerMsg;

}