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
use Psr\Log\LoggerInterface;
use Commune\Framework\ASession;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Shell\Session;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session\SessionStorage;
use Commune\Blueprint\Configs\ShellConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Shell $shell
 * @property-read ShellConfig $config
 * @property-read ReqContainer $container
 * @property-read Session\ShellStorage $storage
 * @property-read LoggerInterface $logger
 * @property-read Cache $cache
 */
class IShellSession extends ASession implements ShellSession
{

    const SINGLETONS =  [
        'logger' => Session\ShellLogger::class,
        'storage' => Session\ShellStorage::class,
        'cache' => Cache::class,
    ];

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


    public function __construct(
        Shell $shell,
        ReqContainer $container,
        string $sessionId
    )
    {
        $this->_shell = $shell;
        $this->_container = $container;
        $this->_config = $shell->getConfig();
        parent::__construct($container, $sessionId);
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
        unset($this->_input);
    }

    protected function saveSession(): void
    {
        // storage 更新.
        if ($this->isSingletonInstanced('storage')) {
            $this->__get('storage')->save();
        }
    }

    /*------- getter -------*/

    public function __get($name)
    {
        switch ($name) {
            case 'config' :
                return $this->_config;
            case 'container' :
                return $this->_container;
            case 'shell' :
                return $this->_shell;
            default :
                return parent::__get($name);
        }
    }

}