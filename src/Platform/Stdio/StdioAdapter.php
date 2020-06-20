<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Stdio;

use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Platform\Adapters\ShellInputAdapter;
use Commune\Ghost\Support\ValidateUtils;
use Commune\Kernel\Protocals\IShellInputRequest;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\HostMsg\DefaultIntents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioAdapter implements ShellInputAdapter
{

    /**
     * @var StdioPacker
     */
    protected $packer;

    /**
     * @var ShellInputRequest
     */
    protected $request;

    /**
     * StdioAdapter constructor.
     * @param StdioPacker $packer
     */
    public function __construct(StdioPacker $packer)
    {
        $this->packer = $packer;
    }

    public function isInvalid(): ? string
    {
        return null;
    }

    public function getRequest(): ShellInputRequest
    {
        if (isset($this->request)) return $this->request;

        $message = IText::instance($this->packer->getInput());

        $platform = $this->packer->getPlatform();
        $option = $platform->getOption();
        $shell = $platform->getShell();

        $input = new IInputMsg([
            // 不可为空.
            'shellName' => $shell->getId(),
            // 传入值允许为空, 则会用 guestId 替代.
            'sessionId' => $guestId = $option->guestId,
            // 通常为空. 除非是客户端传来一个明确的 conversationId
            'convoId' => '',
            // 不可为空.
            'guestId' => $guestId,
            // 允许为空. 有的客户端没有 guestName
            'guestName' => $option->guestName,
            // 默认的消息
            'message' => $message,
        ]);

        return $this->request = IShellInputRequest::instance($input);
    }

    /**
     *
     * @param ShellOutputResponse $response
     */
    public function sendResponse($response): void
    {
        ValidateUtils::isArgInstanceOf($response, ShellOutputResponse::class, true);

        $writer = $this->packer->getPlatform()->getWriter();
        $outputs = $response->getOutputs();


        $quit = false;
        foreach ($outputs as $output) {

            $hostMsg = $output->getMessage();
            $level = $hostMsg->getLevel();
            $writer->log($level, $hostMsg->getText() . "\n");

            if ($hostMsg->getProtocalId() === DefaultIntents::SYSTEM_SESSION_QUIT ) {
                $quit = true;
            }
        }


        if (!$response->isSuccess()) {
            $code = $response->getErrcode();
            $message = $response->getErrmsg();

            $writer->emergency("code: $code, message: $message");
            $quit = true;
        }


        if ($quit) {
            $stdio = $this->packer->getPlatform()->getStdio();
            $stdio->end('quit');
        }
    }


}