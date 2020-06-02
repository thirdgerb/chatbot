<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ClonePipes;

use Commune\Blueprint\CommuneEnv;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AClonePipe implements RequestPipe
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $cloner;

    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
        $this->logger = $cloner->logger;
    }

    abstract protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse;

    /**
     * @param AppRequest $request
     * @param \Closure $next
     * @return GhostResponse
     */
    public function handle(AppRequest $request, \Closure $next): AppResponse
    {
        if (!$request instanceof GhostRequest) {
            throw new InvalidArgumentException('request is not instance of '. GhostRequest::class);
        }

        $debug = CommuneEnv::isDebug();

        if ($debug) {
            $a = microtime(true);
        }

        // 如果已经 quit, 就不往后走了.
        if ($this->cloner->isQuit()) {
            return $request->success($this->cloner);
        }

        $response = $this->doHandle($request, $next);

        if ($debug && isset($a)) {
            $b = microtime(true);
            $gap = round(($b - $a) * 1000000);
            $pipeName = static::class;
            $this->logger->debug("$pipeName end pipe gap: {$gap}ws");
        }
        return $response;
    }
}