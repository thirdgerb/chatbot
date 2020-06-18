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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostRequest implements GhostRequest
{
    use TAppRequest;

    /**
     * @var InputMsg
     */
    protected $input;

    /**
     * @var bool
     */
    protected $async;

    /**
     * @var bool
     */
    protected $tiny;

    /**
     * @var bool
     */
    protected $stateless;

    /**
     * ICloneRequest constructor.
     * @param InputMsg $input
     * @param bool $async
     * @param bool $tiny
     * @param bool $stateless
     */
    public function __construct(InputMsg $input, bool $async, bool $tiny, bool $stateless = null)
    {
        $this->input = $input;
        $this->async = $async;
        $this->tiny = $tiny;
        $this->stateless = $stateless;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function requireTinyResponse(): bool
    {
        return $this->tiny;
    }


    public function getTraceId(): string
    {
        return $this->getInput()->getTraceId();
    }

    public function isStateless(): bool
    {
        return $this->stateless
            ?? $this->getInput()->getMessage() instanceof HostMsg\Convo\ApiMsg;
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    public function noContent(): GhostResponse
    {
        return new ICloneResponse(
            $this->getTraceId(),
            $this->requireTinyResponse()
        );
    }

    public function success(Cloner $cloner): GhostResponse
    {
        return new ICloneResponse(
            $this->getTraceId(),
            $this->requireTinyResponse(),
            AppResponse::SUCCESS,
            '',
            $cloner->input,
            $cloner->getAsyncInputs(),
            $cloner->getOutputs()
        );
    }


    public function output(HostMsg $message, HostMsg ...$messages): GhostResponse
    {
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
            null,
            [],
            $outputs
        );
    }

    public function fail(int $errcode, string $errmsg = ''): GhostResponse
    {
        return new ICloneResponse(
            $this->getTraceId(),
            $this->requireTinyResponse(),
            $errcode,
            $errmsg
        );
    }


}