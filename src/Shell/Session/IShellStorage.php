<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Session;

use Commune\Blueprint\Shell\Session\ShellStorage;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Framework\Session\ASessionStorage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellStorage extends ASessionStorage implements ShellStorage
{

    protected $_data = [
        'cloneSessionId' => null,
    ];

    public function __construct(ShellSession $session)
    {
        parent::__construct($session);
    }

    public function isStateless(): bool
    {
        return false;
    }

    public function getSessionKey(string $appId, string $sessionId): string
    {
        return "shell:$appId:id:$sessionId:storage";
    }


}