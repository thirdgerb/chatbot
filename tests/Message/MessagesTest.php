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
use Commune\Kernel\Protocols;

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
        Message\Host\Convo\Media\IVideoMsg::class,

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
        Message\Intercom\IInputMsg::class,
        Message\Intercom\IOutputMsg::class,

        // qa
        Message\Host\Convo\QA\IQuestionMsg::class,
        Message\Host\Convo\QA\IAnswerMsg::class,
        Message\Host\Convo\QA\IChoose::class,
        Message\Host\Convo\QA\IChoice::class,
        Message\Host\Convo\QA\IConfirm::class,
        Message\Host\Convo\QA\IConfirmation::class,
        Message\Host\Convo\QA\IStepper::class,
        Message\Host\Convo\QA\IStep::class,

        // api
        Message\Host\IApiMsg::class,

        // kernel Protocols
        Protocols\IGhostRequest::class,
        Protocols\IGhostResponse::class,
        Protocols\IShellInputRequest::class,
        Protocols\IShellInputResponse::class,
        Protocols\IShellOutputRequest::class,
        Protocols\IShellOutputResponse::class,
    ];


}