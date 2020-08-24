<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Blueprint;

use Commune\Blueprint\Ghost\MindDef\ChatDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Components\SpaCyNLU\Protocals\ChatReplyData;
use Commune\Components\SpaCyNLU\Protocals\IntentPredictionData;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SpaCyNLUClient
{

    /**
     * @param IntentDef $def
     * @return null|string  error message
     */
    public function intentLearn(IntentDef $def) : ? string;

    /**
     * @param string $sentence
     * @param array $possibles
     * @param float $threshold
     * @param int $limit
     * @return IntentPredictionData[]
     */
    public function intentPredict(
        string $sentence,
        array $possibles,
        float $threshold,
        int $limit = 5
    ) : array ;

    /**
     * @param ChatDef $def
     * @return null|string
     */
    public function chatLearn(ChatDef $def) : ? string;

    /**
     * @param string $say
     * @param float $threshold
     * @param string $index
     * @return null|ChatReplyData
     */
    public function chatReply(
        string $say,
        float $threshold,
        string $index = ''
    ) : ? ChatReplyData;

}