<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell;

use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Shell;
use Commune\Contracts\Cache;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Shell\Session\ShellStorage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Shell $shell
 * @property-read ShellConfig $config
 * @property-read ShellStorage $storage
 * @property-read LoggerInterface $logger
 * @property-read Cache $cache
 * @property-read ReqContainer $container
 */
interface ShellSession extends Session
{
    public function isSingletonInstanced($name) : bool;
}