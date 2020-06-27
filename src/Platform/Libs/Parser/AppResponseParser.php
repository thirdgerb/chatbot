<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\Parser;

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\HasOutputs;
use Commune\Framework\Log\IConsoleLogger;
use Commune\Protocals\HostMsg\DefaultIntents;
use Commune\Protocals\HostMsg\IntentMsg;
use Psr\Log\LogLevel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AppResponseParser
{

    public static function outputsToString(AppResponse $response) : string
    {
        $outputStr = '';
        $code = $response->getErrcode();
        if (
            !$response instanceof HasOutputs
            || $code !== AppResponse::SUCCESS
        ) {
            $errmsg = $response->getErrmsg();
            $outputStr .= IConsoleLogger::wrapMessage(
                LogLevel::CRITICAL,
                "request failed, code $code, msg $errmsg \n\n"
            );
            return $outputStr;
        }

        $outputs = $response->getOutputs();

        foreach ($outputs as $output) {
            $message = $output->getMessage();
            $text = $message->getText();
            $level = $message->getLevel();

            $outputStr .= IConsoleLogger::wrapMessage(
                $level,
                $text
            );

            $outputStr .= "\n\n";
        }

        return $outputStr;

    }
}