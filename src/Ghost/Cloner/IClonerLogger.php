<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Log\ContextLogger;
use Commune\Framework\Spy\SpyAgency;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerLogger extends ContextLogger implements ClonerLogger
{
    /**
     * @var ContainerContract
     */
    protected $app;

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

    public function __construct(LoggerInterface $logger, ContainerContract $app)
    {
        $this->app = $app;
        $this->logger = $logger;
        SpyAgency::incr(static::class);
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getReporter(): ExceptionReporter
    {
        return $this->reporter
            ?? $this->reporter = $this->app->get(ExceptionReporter::class);
    }

    protected function getContext(): array
    {
        if (isset($this->context)) {
            return $this->context;
        }
        $context = $this->app->get(ClonerScope::class)->toArray();;
        $context['peak'] = memory_get_peak_usage();
        return $this->context = $context;
    }

    public function __destruct()
    {
        $this->context = [];
        $this->app = null;
        $this->logger = null;
        SpyAgency::decr(static::class);
    }

}