<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers\Stage;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Components\Markdown\Analysers\StageAnalyser;
use Commune\Components\Markdown\Mindset\SectionStageDef;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageEventAls implements StageAnalyser
{
    /**
     * @var ConsoleLogger
     */
    protected $logger;

    /**
     * StageEventAls constructor.
     * @param ConsoleLogger $logger
     */
    public function __construct(ConsoleLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $content    eventName | handler
     * @param SectionStageDef $def
     * @return StageDef
     */
    public function __invoke(
        string $content,
        SectionStageDef $def
    ): StageDef
    {
        list($eventName, $handler) = explode(' ', $content, 2);

        $eventName = trim($eventName);
        $handler = trim($handler);

        if (
            !$this->isValidDialogEvent($eventName)
            || !$this->isValidHandler($handler)
        ) {

            $this->logger->warning(
                static::class . '::'. __FUNCTION__
                . " invalid comment content $content"
            );
            return $def;
        }

        $events = $def->events;
        $events = [$eventName => $handler] + $events;
        $def->events = $events;
        return $def;
    }

    protected function isValidHandler(string $handler) : bool
    {
        if (is_callable($handler)) {
            return true;
        }
        return TypeUtils::isInvokerClass($handler);
    }

    protected function isValidDialogEvent(string $eventName) : bool
    {
        if (is_a($eventName, Dialog::class, true)) {
            return true;
        }

        return defined(Dialog::class . '::' . $eventName);
    }


}