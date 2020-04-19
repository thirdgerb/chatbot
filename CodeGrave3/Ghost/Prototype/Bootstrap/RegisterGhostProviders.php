<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Bootstrap;

use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Exceptions\BootingException;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\GhostConfig;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RegisterGhostProviders implements Bootstrapper
{
    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * @var GhostConfig
     */
    protected $config;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * RegisterProviders constructor.
     * @param Ghost $ghost
     * @param GhostConfig $config
     * @param LogInfo $logInfo
     */
    public function __construct(
        Ghost $ghost,
        GhostConfig $config,
        LogInfo $logInfo
    )
    {
        $this->ghost = $ghost;
        $this->config = $config;
        $this->logInfo = $logInfo;
    }


    public function bootstrap(): void
    {
        foreach ($this->config->providers as $index => $value) {

            if (is_string($index) && is_array($value)) {
                $this->ghost->registerProvider(
                    $index,
                    $value
                );
            } elseif (is_string($value)) {
                $this->ghost->registerProvider($value);

            } else {

                throw new BootingException(
                  $this->logInfo->bootInvalidProviderConfiguration($index, $value)
                );
            }
        }

    }


}