<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Convo;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Protocals\HostMsg\ConvoMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface QuestionMsg extends ConvoMsg
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
     * @param $index
     * @param $suggestion
     * @param string|null $stageName
     */
    public function addSuggestion($index, $suggestion, string $stageName = null) : void;

    /**
     * @param Cloner $cloner
     */
    public function parse(Cloner $cloner)  :void;

}