<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MessageAnalyser extends AnalyserInterface
{

    /**
     * @param string $content
     * @param array $bufferedLines      系统正准备发送的消息. 如果要打断, 可自行发送.
     * @param Dialog $dialog
     * @return Operator|null
     */
    public function __invoke(
        string $content,
        array &$bufferedLines,
        Dialog $dialog
    ) : ? Operator;


}