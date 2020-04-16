<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Prototype\Session\ASessionStorage;
use Commune\Message\Blueprint\QuestionMsg;
use Commune\Shell\Blueprint\Session\ShellSession;
use Commune\Shell\Blueprint\Session\ShellStorage;
use Commune\Support\Babel\Babel;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellStorage extends ASessionStorage implements ShellStorage
{

    const SESSION_DATA_KEY = 'shell:%s:session:%s:storage';
    const QUESTION_KEY = 'shellQuestion';


    public function setQuestion(QuestionMsg $question): void
    {
        $this->set(static::QUESTION_KEY, Babel::getResolver()->serialize($question));
    }

    public function getQuestion(): ? QuestionMsg
    {
        $value = $this->get(static::QUESTION_KEY);

        return isset($value)
            ? Babel::getResolver()->unSerialize($value)
            : null;
    }

    /**
     * @param ShellSession $session
     * @return string
     */
    protected function makeSessionCacheKey(Session $session): string
    {
        return printf(
            static::SESSION_DATA_KEY,
            $session->shell->getShellName(),
            $session->getSessionId()
        );
    }


}