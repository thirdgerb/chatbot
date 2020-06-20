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
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Framework\Session\ASessionStorage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerStorage extends ASessionStorage implements ClonerStorage
{
    /**
     * @var Cloner
     */
    protected $session;

    protected $_data = [
        'requestFailTimes' => 0,
        'shellSessionRoutes' => []
    ];

    public function __construct(Cloner $session)
    {
        parent::__construct($session);
    }

    public function getSessionKey(string $appId, string $sessionId): string
    {
        return "ghost:$appId:id:$sessionId:storage";
    }

    public function isStateless(): bool
    {
        return $this->session->isStateless();
    }


}