<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerGuest;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerGuest implements ClonerGuest
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * IClonerGuest constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }


    public function getId(): string
    {
        return $this->cloner->input->getCreatorId();
    }

    public function getName(): string
    {
        return $this->cloner->input->getCreatorName();
    }

    public function isFromBot(): bool
    {
        return $this->cloner->input->isFromBot();
    }

    public function fromApp(): string
    {
        $container = $this->cloner->container;
        $request = $container->bound(GhostRequest::class)
            ? $container->make(GhostRequest::class)
            : null;

        return $request instanceof GhostRequest
            ? $request->getFromApp()
            : '';

    }

    public function fromSession(): string
    {
        $container = $this->cloner->container;
        $request = $container->bound(GhostRequest::class)
            ? $container->make(GhostRequest::class)
            : null;

        return $request instanceof GhostRequest
            ? $request->getFromSession()
            : '';
    }

    public function getLevel(): int
    {
        return $this->cloner->env->userLevel;
    }

    public function getInfo(): array
    {
        return $this->cloner->env->userInfo;
    }


}