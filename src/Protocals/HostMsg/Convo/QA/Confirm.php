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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Contracts\Trans\SystemLang;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Confirm extends QuestionMsg
{
    const DEFAULT_POSITIVE = 'y';
    const DEFAULT_NEGATIVE = 'n';

    const POSITIVE_LANG = SystemLang::POSITIVE_LANG;
    const NEGATIVE_LANG = SystemLang::NEGATIVE_LANG;

    public function setPositive(string $suggestion, Ucl $ucl = null) : Confirm;

    public function setNegative(string $suggestion, Ucl $ucl = null) : Confirm;

    public function getPositiveSuggestion() : string;

    public function getNegativeSuggestion() : string;

}