<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell;

use Commune\Blueprint\Shell;
use Commune\Contracts\Cache;
use Commune\Protocals\Intercom\OutputMsg;
use Psr\Log\LoggerInterface;
use Commune\Framework\ASession;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Shell\Session;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session\SessionStorage;
use Commune\Blueprint\Configs\ShellConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellSession extends ASession implements ShellSession
{

    const SINGLETONS =  [

        'logger' => Session\ShellLogger::class,
        'storage' => Session\ShellStorage::class,
        'cache' => Cache::class,
    ];


    /**
     * @var InputMsg
     */
    protected $_input;

    /**
     * @var ReqContainer
     */
    protected $_container;

    /**
     * @var Shell
     */
    protected $_shell;

    /**
     * @var ShellConfig
     */
    protected $_config;

    /**
     * @var int
     */
    protected $_cacheExpire;


    public function __construct(Shell $shell, ReqContainer $container, InputMsg $input)
    {
        $this->_input = $input;
        $this->_shell = $shell;
        $this->_container = $container;
        $this->_config = $shell->getConfig();
        parent::__construct($container, $input->getSessionId());
    }

    /*------ getter ------*/

    public function getApp(): App
    {
        return $this->_shell;
    }

    public function getStorage(): SessionStorage
    {
        return $this->__get('storage');
    }

    public function getLogger(): LoggerInterface
    {
        return $this->__get('logger');
    }

    /*------ lock ------*/

    public function lock(int $second): bool
    {
        $ttl = $this->_config->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getShellLockerKey();
            return $this->__get('cache')->lock($locker, $ttl);
        } else {
            return true;
        }
    }

    protected function getShellLockerKey() : string
    {
        $shellId = $this->getAppId();
        $shellSessionId = $this->getSessionId();

        return "shell:$shellId:session:$shellSessionId:locker";
    }

    public function isLocked(): bool
    {
        $ttl = $this->_config->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getShellLockerKey();
            return $this->__get('cache')->lock($locker, $ttl);
        } else {
            return true;
        }
    }

    public function unlock(): bool
    {
        $ttl = $this->_config->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getShellLockerKey();
            return $this->__get('cache')->has($locker);
        }
        return false;
    }

    /*------ expire ------*/

    public function getSessionExpire(): int
    {
        return $this->_cacheExpire
            ?? $this->_cacheExpire = $this->_config->sessionExpire;
    }

    public function setSessionExpire(int $seconds): void
    {
        $this->_cacheExpire = $seconds;
    }


    /*------ save ------*/

    protected function flushInstances(): void
    {
        unset($this->_config);
        unset($this->_shell);
        unset($this->_container);
        unset($this->_input);
    }

    protected function saveSession(): void
    {
        // storage 更新.
        if ($this->isSingletonInstanced('storage')) {
            $this->__get('storage')->save();
        }
    }


}