<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Message;

use Commune\Message;
use Commune\Support\Message\MessageTestCase;
use Commune\Kernel\Protocals;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessagesTest extends MessageTestCase
{
    protected $messages = [
        // convo
        Message\Host\Convo\IText::class,
        Message\Host\Convo\IUnsupportedMsg::class,
        Message\Host\Convo\IEventMsg::class,
        Message\Host\Convo\IAudioMsg::class,
        Message\Host\Convo\IImageMsg::class,
        Message\Host\Convo\IContextMsg::class,

        // intent
        Message\Host\IIntentMsg::class,
        Message\Host\SystemInt\CommandDescInt::class,
        Message\Host\SystemInt\CommandErrorInt::class,
        Message\Host\SystemInt\CommandListInt::class,
        Message\Host\SystemInt\CommandMissInt::class,
        Message\Host\SystemInt\RequestFailInt::class,
        Message\Host\SystemInt\SessionBusyInt::class,
        Message\Host\SystemInt\SessionQuitInt::class,
        Message\Host\SystemInt\SessionFailInt::class,
        Message\Host\SystemInt\DialogRequireInt::class,
        Message\Host\SystemInt\DialogConfuseInt::class,
        Message\Host\SystemInt\DialogYieldInt::class,
        Message\Host\SystemInt\DialogForbidInt::class,

        // intercom
//        Message\Intercom\IInputMsg::class,
//        Message\Intercom\IOutputMsg::class,

        // qa
        Message\Host\QA\IQuestionMsg::class,
        Message\Host\QA\IAnswerMsg::class,
        Message\Host\QA\IChoose::class,
        Message\Host\QA\IChoice::class,
        Message\Host\QA\IConfirm::class,
        Message\Host\QA\IConfirmation::class,


        // kernel protocals
//        Protocals\IGhostRequest::class,
//        Protocals\IGhostResponse::class,
//        Protocals\IShellInputRequest::class,
//        Protocals\IShellInputResponse::class,
//        Protocals\IShellOutputRequest::class,
//        Protocals\IShellOutputResponse::class,
    ];


}