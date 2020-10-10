<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ClonePipes;

use Commune\Blueprint\CommuneEnv;
use Commune\Framework\Spy\SpyAgency;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AClonePipe implements RequestPipe
{

    /**
     * @var Cloner
     */
    protected $cloner;

    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
        SpyAgency::incr(self::class);
    }

    abstract protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse;

    /**
     * @param AppRequest $request
     * @param \Closure $next
     * @return GhostResponse
     */
    public function handle(AppRequest $request, \Closure $next): AppResponse
    {
        $debug = CommuneEnv::isDebug();

        if ($debug) {
            $a = microtime(true);
        }

        if ($request instanceof GhostRequest) {
            $response = $this->doHandle($request, $next);

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
            $this->cloner->logger->debug("$pipeName end pipe gap: {$gap}us");
        }
        return $response;
    }

    public function __destruct()
    {
        SpyAgency::decr(self::class);
    }
}