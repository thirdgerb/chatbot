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

use Commune\Protocals\Abstracted\Intention;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\Intercom\InputMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait ParserTrait
{

    public function parse(
        InputMsg $message,
        Comprehension $comprehension
    ): Comprehension
    {
        $msg = $message->getMessage();
        if (!$msg instanceof VerbalMsg) {
            return $comprehension;
        }

        $text = $message->getNormalizedText();
        if (NLUUtils::isNotNatureLanguage($text)) {
            return $comprehension;
        }

        $handled = $comprehension->isHandled(Intention::class);
        if ($handled) {
            return $comprehension;
        }

        return $this->doParse(
            $message,
            $text,
            $comprehension
        );
    }

    abstract protected function doParse(
        InputMsg $input,
        string $normalizedText,
        Comprehension $comprehension
    ) : Comprehension;



}