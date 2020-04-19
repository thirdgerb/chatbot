<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Bootstrap;

use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Exceptions\BootingException;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\ShellConfig;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RegisterShellProviders implements Bootstrapper
{

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var ShellConfig
     */
    protected $config;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * RegisterGhostProviders constructor.
     * @param Shell $shell
     * @param ShellConfig $config
     * @param LogInfo $logInfo
     */
    public function __construct(Shell $shell, ShellConfig $config, LogInfo $logInfo)
    {
        $this->shell = $shell;
        $this->config = $config;
        $this->logInfo = $logInfo;
    }


    public function bootstrap(): void
    {
        foreach ($this->config->providers as $index => $value) {

            if (is_string($index) && is_array($value)) {
                $this->shell->registerProvider(
                    $index,
                    $value
                );
            } elseif (is_string($value)) {
                $this->shell->registerProvider($value);

            } else {

                throw new BootingException(
                  $this->logInfo->bootInvalidProviderConfiguration($index, $value)
                );
            }
        }

    }


}