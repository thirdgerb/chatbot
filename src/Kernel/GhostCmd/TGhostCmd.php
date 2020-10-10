<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Container\ContainerContract;
use Commune\Protocols\HostMsg;

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
    protected $stillToNextPipe = false;

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

        if (!$this->stillToNextPipe) {
            $this->cloner->noState();
            $avatar = $this->cloner->avatar;
            return $request->output(
                $avatar->getId(),
                $avatar->getName(),
                ...$this->outputs
            );

        } else {

            $input = $request->getInput();
            foreach ($messages as $message) {
                $this->cloner->output($input->output($message));
            }

            return null;
        }
    }

    public function goNext() : void
    {
        $this->stillToNextPipe = true;
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