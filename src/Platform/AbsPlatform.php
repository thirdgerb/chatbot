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

use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Host;
use Commune\Blueprint\Platform;
use Psr\Log\LoggerInterface;
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
     * @var ProcContainer
     */
    protected $procC;

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

    /**
     * 响应请求.
     *
     * @param Platform\Adapter $adapter
     * @param AppRequest $request
     * @param string|null $interface
     */
    abstract protected function handleRequest(
        Platform\Adapter $adapter,
        AppRequest $request,
        string $interface = null
    ) : void;

    /**
     * @return ProcContainer
     */
    public function getContainer(): ProcContainer
    {
        return $this->procC
            ?? $this->procC = $this->host->getProcContainer();
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    public function onPacker(
        Platform\Packer $packer,
        string $adapterName,
        string $interface = null
    ) : bool
    {
        try {
            // 检查数据包是否合法.
            $requestError = $packer->isInvalid();
            if (isset($requestError)) {
                return $this->donePacker($packer, $requestError);
            }

            // 检查协议是否合法.
            $appId = $this->getAppId();
            $adapter = $packer->adapt($adapterName, $appId);

            $requestError = $adapter->isInvalid();
            if (isset($requestError)) {
                $adapter->destroy();
                return $this->donePacker($packer, $requestError);
            }

            $request = $adapter->getRequest();

            $this->handleRequest($adapter, $request, $interface);
            $adapter->destroy();

            return $this->donePacker($packer);

        } catch (\Throwable $e) {

            $this->catchExp($e);
            if (isset($adapter)) $adapter->destroy();

            return $this->donePacker($packer, $e->getMessage());
        }
    }

    protected function donePacker(Platform\Packer $packer, string $error = null) : bool
    {
        $failed = isset($failed);
        if ($failed) {
            $packer->fail($error);
            // 记录日志.
            $this->invalidRequest($error);
        }

        $packer->destroy();
        return ! $failed;
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