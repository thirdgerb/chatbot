<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cmd;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Container\ContainerContract;
use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TGhostCmd
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var bool
     */
    protected $goNext = false;

    /**
     * TGhostCmd constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }

    protected function checkRequest(AppRequest $request): void
    {
        if (!$request instanceof GhostRequest) {
            throw new InvalidArgumentException('ghost command only accept ' . GhostRequest::class);
        }
    }

    /**
     * @param AppRequest $request
     * @param HostMsg[] $messages
     * @return AppResponse|null
     */
    protected function response(AppRequest $request, array $messages): ? AppResponse
    {
        if (empty($messages)) {
            return null;
        }

        if (!$request instanceof GhostRequest) {
            return null;
        }

        $input = $request->getInput();
        foreach ($messages as $message) {
            $this->cloner->output($input->output($message));
        }

        if ($this->goNext) {
            $this->cloner->noState();
            return null;

        } else {
            return $request->response($this->cloner);
        }
    }

    public function goNext() : void
    {
        $this->goNext = true;
    }

    public function getContainer(): ContainerContract
    {
        return $this->cloner->container;
    }

    protected function getCloner() : Cloner
    {
        return $this->cloner;
    }


}