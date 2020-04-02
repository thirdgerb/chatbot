<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Pipeline;

use Commune\Shell\Blueprint\Pipeline\ShellPipe;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Prototype\Events\StartShlPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellPipe implements ShellPipe
{


    abstract public function doHandle(ShlSession $session, callable $next): ShlSession;

    public function handle(ShlSession $session, callable $next): ShlSession
    {
        $session->fire(new StartShlPipe($this));
        $debug = $session->shell->isDebugging();

        if (!$debug) {
            $session = $this->doHandle($session, $next);
            return $session;
        }

        $pipeName = static::class;
        $start = microtime(true);

        $session = $this->doHandle($session, $next);

        $end = microtime(true);
        $gap = abs(intval(($end - $start) * 1000));

        $session->logger->info(
            "end chat pipe",
            [
                'pipe' => $pipeName,
                'gap' => $gap,
                'mem' => memory_get_usage(true)
            ]
        );

        return $session;
    }


}