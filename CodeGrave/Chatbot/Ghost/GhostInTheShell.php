<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost;

use Commune\Chatbot\Ghost\Blueprint\Dialog;
use Commune\Chatbot\Ghost\Blueprint\Ghost;
use Commune\Chatbot\Ghost\Blueprint\Memory;
use Commune\Chatbot\Ghost\Blueprint\Mind;
use Commune\Chatbot\Ghost\Blueprint\Session;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Session $session  会话
 * @property-read Mind $mind 思维
 * @property-read Dialog $dialog 对话管理
 * @property-read Memory $memory 记忆单元
 *
 */
class GhostInTheShell implements Ghost
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Mind
     */
    protected $mind;


    /**
     * @var Memory
     */
    protected $memory;

    /**
     * @var null|Dialog
     */
    protected $dialog;

    /**
     * FPHost constructor.
     * @param Session $session
     * @param Mind $mind
     */
    public function __construct(Session $session, Mind $mind)
    {
        $this->session = $session;
        $this->mind = $mind;
    }


    public function syncResponse(): Session
    {
    }


    public function __get($name)
    {
        switch ($name) {
            case 'session' :
                return $this->session;
            case 'mind' :
                return $this->mind;
            case 'dialog' :
                return $this->dialog
                    ?? $this->dialog = $this->session
                        ->conversation
                        ->make(Dialog::class);
            case 'memory' :
                return $this->memory
                    ?? $this->memory = $this->session
                        ->conversation
                        ->make(Memory::class);
            default:
                return null;
        }
    }


}