<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform;

use Commune\Blueprint\Host;
use Commune\Blueprint\Platform;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Kernel\Protocals\AppRequest;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsPlatform implements Platform
{
    /**
     * @var Host
     */
    protected $host;

    /**
     * @var PlatformConfig
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbsPlatform constructor.
     * @param Host $host
     * @param PlatformConfig $config
     * @param LoggerInterface $logger
     */
    public function __construct(Host $host, PlatformConfig $config, LoggerInterface $logger)
    {
        $this->host = $host;
        $this->config = $config;
        $this->logger = $logger;
    }

    abstract protected function handleRequest(Platform\Adapter $adapter, AppRequest $request) : void;


    public function onPacker(Platform\Packer $packer) : void
    {
        try {
            // 检查数据包是否合法.
            $requestError = $packer->isInvalid();
            if (isset($requestError)) {
                $this->invalidRequest($requestError);
            }

            // 检查协议是否合法.
            $adapter = $packer->adapt($this->getConfig()->adapter);
            $requestError = $adapter->isInvalid();
            if (isset($requestError)) {
                $this->invalidRequest($requestError);
            }

            $request = $adapter->getRequest();
            $this->handleRequest($adapter, $request);

        } catch (\Throwable $e) {
            $packer->fail($e);
            $this->catchExp($e);
        }
    }



    public function getConfig(): PlatformConfig
    {
        return $this->config;
    }

    public function getId(): string
    {
        return $this->config->id;
    }

    protected function invalidRequest(string $error) : void
    {
        $id = $this->getId();
        $this->logger->notice("invalid request for platform $id, $error");
    }

    public function catchExp(\Throwable $e): void
    {
        $this->host->getConsoleLogger()->critical(strval($e));
    }


}