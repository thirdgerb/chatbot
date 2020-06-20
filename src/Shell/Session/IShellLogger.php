<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Session;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Shell\Session\ShellLogger;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Log\ContextLogger;
use Commune\Framework\Spy\SpyAgency;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellLogger extends ContextLogger implements ShellLogger
{
    /**
     * @var ContainerContract
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ExceptionReporter
     */
    protected $reporter;

    /**
     * @var array
     */
    protected $context;

    public function __construct(
        LoggerInterface $logger,
        ContainerContract $container
    )
    {
        $this->container = $container;
        $this->logger = $logger;
        SpyAgency::incr(static::class);
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    protected function report(\Throwable $e): void
    {
        $this->getReporter()->report($e);
    }

    protected function getReporter(): ExceptionReporter
    {
        return $this->reporter
            ?? $this->reporter = $this->container->get(ExceptionReporter::class);
    }

    protected function makeContext(): array
    {
        $context = [];

        if ($this->container->bound(AppRequest::class)) {
            $request = $this->container->get(AppRequest::class);
            $context['traceId'] = $request->getTraceId();
            $context['sessionId'] = $request->getSessionId();
        }

        return $context;
    }

    public function __destruct()
    {
        unset($this->context);
        unset($this->logger);
        SpyAgency::decr(static::class);
    }

}
