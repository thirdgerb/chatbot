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

    public function __construct(Cloner $session)
    {
        parent::__construct($session);
    }

    public function getSessionKey(string $sessionName, string $sessionId): string
    {
        return "ghost:$sessionName:id:$sessionId:storage";
    }

    protected function initDataFromCache(): void
    {
        parent::initDataFromCache();

        // 从 env 中获取 shell data 并保存到 session storage 中.

        $env = $this->session->scene->env;
        $key = ClonerStorage::SHELL_DATA;
        $shellData = $env[$key] ?? [];

        if (!empty($shellData)) {
            $stored = $this->data[$key] ?? [];
            $stored = $shellData + $stored;
            $this->data[$key] = $stored;
        }
    }


}