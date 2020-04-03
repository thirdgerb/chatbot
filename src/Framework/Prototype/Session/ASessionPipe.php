<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Session;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionPipe;
use Commune\Framework\Prototype\Session\Events\EndSessionPipe;
use Commune\Framework\Prototype\Session\Events\StartSessionPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionPipe implements SessionPipe
{
    /**
     * @param Session $session
     * @return Session
     */
    abstract protected function before($session);

    /**
     * @param Session $session
     * @return Session
     */
    abstract protected function after($session);

    public function handle(Session $session, callable $next): Session
    {
        $session->fire(new StartSessionPipe($this));
        $debug = $session->getApp()->isDebugging();

        if ($debug) {
            $start = microtime(true);
        }

        $session = $this->before($session);
        $session = $this->next($session, $next);

        if (isset($start)) {
            $pipeName = static::class;
            // 记录时间.
            $end = microtime(true);
            $gap = abs(intval(($end - $start) * 1000));

            $session->getLogger()->info(
                "end ghost pipe",
                [
                    'pipe' => $pipeName,
                    'gap' => $gap,
                    'mem' => memory_get_usage(true)
                ]
            );
        }

        return $session;
    }

    protected function next(Session $session, callable $next): Session
    {
        // 结束了就没有下一步了.
        if (!$session->isFinished()) {
            $session = $next($session);
        }

        // 结束了就没有 after 环节了.
        if (!$session->isFinished()) {
            $session = $this->after($session);
        }

        $session->fire(new EndSessionPipe($this));
        return $session;
    }


}