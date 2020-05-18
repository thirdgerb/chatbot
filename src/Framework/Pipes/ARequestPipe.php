<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Pipes;

use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Framework\Pipes\RequestPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ARequestPipe implements RequestPipe
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ARequestPipe constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    abstract protected function doHandle(AppRequest $request, \Closure $next): AppResponse;

    public function handle(AppRequest $request, \Closure $next): AppResponse
    {
        $a = microtime(true);

        $response = $this->doHandle($request, $next);

        $b = microtime(true);
        $gap = round(($b - $a) * 1000000);
        $pipeName = static::class;

        $this->logger->debug("$pipeName end pipe gap: {$gap}ws");
        return $response;
    }


}