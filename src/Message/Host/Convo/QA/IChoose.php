<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\QA;

use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\QA\Choose;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChoose extends IQuestionMsg implements Choose
{
    protected $acceptAnyTextAsValue = false;

    public static function newChoose(
        string $query,
        $default = null,
        array $suggestions = [],
        array $routes = []
    ): IQuestionMsg
    {
        return parent::instance($query, $default, $suggestions, $routes);
    }

    protected function makeAnswerInstance(
        string $answer,
        $choice = null,
        string $route = null
    ): AnswerMsg
    {
        return new IChoice([
            'answer' => $answer,
            'choice' => $choice,
            'route' => $route,
        ]);
    }
}