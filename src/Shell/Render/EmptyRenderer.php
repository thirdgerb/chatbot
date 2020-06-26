<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Render;

use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Protocals\HostMsg;
use Psr\Log\LoggerInterface;

/**
 * 把消息给吞掉, 装作没有过.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class EmptyRenderer implements Renderer
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * EmptyRenderer constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(HostMsg $message): ? array
    {
        $class = get_class($message);
        $id = $message->getProtocalId();

        $this->logger->info(
            __METHOD__
            . " eat message $class : $id"
        );
        return [];
    }

}