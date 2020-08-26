<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Auth;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Cloner;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ISupervise implements Supervise
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * ISupervise constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }

    public function invoke(array $payload = []): bool
    {
        return $this->cloner->scene->userLevel >= Supervise::SUPERVISOR;
    }


}