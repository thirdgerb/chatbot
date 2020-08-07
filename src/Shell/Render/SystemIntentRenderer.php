<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Render;

use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Message\Host\SystemInt\SessionSyncInt;
use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SystemIntentRenderer implements Renderer
{

    /**
     * @var ShellSession
     */
    protected $shellSession;

    /**
     * @var TranslatorRenderer
     */
    protected $translator;

    /**
     * SystemIntentRenderer constructor.
     * @param ShellSession $shellSession
     * @param TranslatorRenderer $translator
     */
    public function __construct(ShellSession $shellSession, TranslatorRenderer $translator)
    {
        $this->shellSession = $shellSession;
        $this->translator = $translator;
    }

    public function __invoke(HostMsg $message): ? array
    {
        // 非系统消息不管.
        if (!$message instanceof HostMsg\IntentMsg) {
            return null;
        }

        $id = $message->getIntentName();

        switch ($id) {
            case DefaultIntents::SYSTEM_SESSION_SYNC :
                return $this->syncSession($message);
            case DefaultIntents::SYSTEM_SESSION_QUIT :
                return $this->quitSession($message);
            case DefaultIntents::SYSTEM_DIALOG_CONFUSE :
                return $this->confuse($message);

            default :
                return $this->translate($message);
        }
    }

    protected function confuse(HostMsg\IntentMsg $message) : array
    {
        $renderer = $this->shellSession->container->make(ConfuseRenderer::class);

        return $renderer($message);
    }

    protected function quitSession(HostMsg\IntentMsg $message) : ? array
    {
        $storage = $this->shellSession->storage;
        unset($storage->cloneSessionId);

        // 先尝试主动清除客户端缓存, 避免复杂逻辑异常. 未来再检视是否保留为宜.
        // 按设计思路, Session 的缓存和 conversation 是分开的.
        // conversation 结束未必要清空 session 的缓存.
        // 但这个和 Ghost 的情况未必一样.
        $this->shellSession->setSessionExpire(0);
        return $this->translate($message);
    }

    protected function syncSession(HostMsg\IntentMsg $message) : ? array
    {
        if ($message instanceof SessionSyncInt) {
            $session = $message->session;
        } else {
            $session = $message->getEntities()['session'] ?? '';
        }

        if (!empty($session)) {
            $this->shellSession->storage->cloneSessionId = $session;
        }

        return $this->translate($message);
    }

    protected function translate(HostMsg\IntentMsg $message) : ? array
    {
        $translator = $this->translator;
        $output = $translator($message);
        return $output;
    }

}