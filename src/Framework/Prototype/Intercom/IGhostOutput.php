<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Intercom;

use Commune\Framework\Blueprint\Intercom\GhostOutput;
use Commune\Framework\Blueprint\Intercom\ShellMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostOutput extends AGhostMsg implements GhostOutput
{
    const PROPERTIES = [
        'chatId' => 'cid',
        'shellName' => 'shn',
        'shellMessage' => 'shm',
        'traceId' => 'tid',
        'messageId' => 'mid',
        'deliverAt' =>  'dlt',
    ];

    /**
     * @var int
     */
    protected $dlt;

    public function __construct(
        string $shellName,
        string $chatId,
        ShellMsg $shellMessage,
        string $traceId,
        int $deliverAt = 0,
        string $messageId = null
    )
    {
        $this->dlt = $deliverAt;
        parent::__construct($shellName, $chatId, $shellMessage, $traceId, $messageId);
    }

    public function __sleep(): array
    {
        $sleeps = parent::__sleep();
        $sleeps[] = 'dlt';
        return $sleeps;
    }

}