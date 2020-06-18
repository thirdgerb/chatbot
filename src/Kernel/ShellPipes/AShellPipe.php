<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ShellPipes;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Framework\Spy\SpyAgency;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellPipe implements RequestPipe
{
    /**
     * @var ShellSession
     */
    protected $session;

    public function __construct(ShellSession $session)
    {
        $this->session = $session;
        SpyAgency::incr(self::class);
    }

    abstract protected function handleInput(
        ShellInputRequest $request,
        \Closure $next
    ): ShellInputResponse;

    abstract protected function handleOutput(
        ShellOutputRequest $request,
        \Closure $next
    ): ShellOutputResponse;

    /**
     * @param AppRequest $request
     * @param \Closure $next
     * @return AppResponse
     */
    public function handle(AppRequest $request, \Closure $next): AppResponse
    {
        $debug = CommuneEnv::isDebug();

        if ($debug) {
            $a = microtime(true);
        }

        if ($request instanceof ShellInputRequest) {
            $response = $this->handleInput($request, $next);

        } elseif ($request instanceof ShellOutputRequest) {
            $response = $this->handleOutput($request, $next);

        } else {
            throw new InvalidArgumentException(
                'request is invalid, '
                . TypeUtils::getType($request)
                . ' given'
            );
        }

        if ($debug && isset($a)) {
            $b = microtime(true);
            $gap = round(($b - $a) * 1000000);
            $pipeName = static::class;
            $this->session->logger->debug("$pipeName end pipe gap: {$gap}us");
        }
        return $response;
    }

    public function __destruct()
    {
        SpyAgency::decr(self::class);
    }

}