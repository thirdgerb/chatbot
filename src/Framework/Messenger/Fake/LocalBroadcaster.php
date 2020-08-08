<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\Fake;

use Commune\Blueprint\Shell;
use Commune\Framework\Messenger\Broadcaster\AbsBroadcaster;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class LocalBroadcaster extends AbsBroadcaster
{
    /**
     * @var Shell
     */
    protected $shell;

    protected $chan = [];

    /**
     * LocalBroadcaster constructor.
     * @param Shell $shell
     * @param LoggerInterface $logger
     */
    public function __construct(Shell $shell, LoggerInterface $logger)
    {
        $this->shell = $shell;
        parent::__construct($logger, []);
    }

    public function doSubscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ): void
    {
        $chan = "$shellId/$shellSessionId";
        while($message = array_shift($this->chan)) {
            $callback($chan, $message);
        }
    }

    public function doPublish(
        string $shellId,
        string $shellSessionId,
        string $publish
    ): void
    {
        array_push($this->chan, $publish);
    }
}