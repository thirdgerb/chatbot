<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Kernel\Protocals;

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read bool $async
 * @property-read bool $tiny
 * @property-read InputMsg $input
 * @property-read bool|null $stateless
 */
class IGhostRequest extends AbsMessage implements GhostRequest
{
    use TAppRequest;

    protected $transferNoEmptyRelations = true;

    protected $transferNoEmptyData = true;

    public static function stub(): array
    {
        return [
            'input' => [],
            'async' => false,
            'tiny' => false,
            'stateless' => null,
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => IInputMsg::class
        ];
    }

    /*-------- Ghost Request --------*/

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function requireTinyResponse(): bool
    {
        return $this->tiny;
    }

    public function output(HostMsg $message, HostMsg ...$messages): CloneResponse
    {
        array_unshift($messages, $message);

        $input = $this->getInput();
        $outputs = array_reduce($messages, function($outputs, HostMsg $message) use ($input){
            $outputs[] = $input->output($message);
            return $outputs;
        }, []);

        return new ICloneResponse(
            $this->getTraceId(),
            $this->requireTinyResponse(),
            AppResponse::SUCCESS,
            '',
            $this->getInput(),
            [],
            $outputs
        );
    }

    public function noContent(): GhostResponse
    {
        return new IGhostResponse([
            'traceId' => '',
            'errcode' => AppResponse::SUCCESS,
            'errmsg' => AppResponse::DEFAULT_ERROR_MESSAGES[AppResponse::SUCCESS],
            'shellName' => '',
            'shellId' => '',
            'batchId' => '',
            'count' => 0,
            'tiny' => false,
            'messages' => []
        ]);
    }

    public function fail(int $errcode, string $errmsg = ''): GhostResponse
    {
        return new IGhostResponse([
            'traceId' => '',
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'shellName' => '',
            'shellId' => '',
            'batchId' => '',
            'count' => 0,
            'tiny' => false,
            'messages' => []
        ]);
    }


    /*-------- App Request --------*/


    public function isStateless(): bool
    {
        return $this->stateless
            ?? $this->getInput()->getMessage() instanceof ApiMsg;
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    /*-------- AppProtocal --------*/

    public function getTraceId(): string
    {
        return $this->getInput()->getMessageId();
    }

    /*-------- AbsMessage --------*/

    public function isEmpty(): bool
    {
        return false;
    }


}