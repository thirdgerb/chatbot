<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\ShellPipes;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Framework\Spy\SpyAgency;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellPipe implements RequestPipe
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ShellSession
     */
    protected $session;


    public function __construct(ShellSession $session)
    {
        $this->session = $session;
        $this->logger = $session->logger;
        SpyAgency::incr(self::class);
    }

    abstract protected function isValidRequest(AppRequest $request) : bool;

    abstract protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse;

    /**
     * @param AppRequest $request
     * @param \Closure $next
     * @return ShellResponse
     */
    public function handle(AppRequest $request, \Closure $next): AppResponse
    {
        if (!$this->isValidRequest($request)) {
            throw new InvalidArgumentException('request is invalid for ' . static::class);
        }

        $debug = CommuneEnv::isDebug();

        if ($debug) {
            $a = microtime(true);
        }

        $response = $this->doHandle($request, $next);

        if ($debug && isset($a)) {
            $b = microtime(true);
            $gap = round(($b - $a) * 1000000);
            $pipeName = static::class;
            $this->logger->debug("$pipeName end pipe gap: {$gap}us");
        }

        return $response;
    }

    public function __destruct()
    {
        SpyAgency::decr(self::class);
    }

}