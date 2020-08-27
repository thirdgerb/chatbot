<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Support;

use Commune\Blueprint\Framework\Session;
use Commune\Protocals\Abstracted\Intention;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\Intercom\InputMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait ParserTrait
{
    protected function getHandlerType() : string
    {
        return Intention::class;
    }

    public function parse(
        InputMsg $input,
        Session $session,
        Comprehension $comprehension
    ): Comprehension
    {
        if (!$input->isMsgType(VerbalMsg::class)) {
            return $comprehension;
        }

        $text = $input->getMessage()->getText();
        $text = trim($text);
        if (NLUUtils::isNotNatureLanguage($text)) {
            return $comprehension;
        }


        $handled = $comprehension->isSucceed($this->getHandlerType());
        if ($handled) {
            return $comprehension;
        }

        return $this->doParse(
            $input,
            $text,
            $session,
            $comprehension
        );
    }

    abstract protected function doParse(
        InputMsg $input,
        string $originText,
        Session $session,
        Comprehension $comprehension
    ) : Comprehension;



}