<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read IntentMeta|null $asIntent
 *
 * @property-read string $title
 * @property-read string $desc
 *
 * @property-read string[] $events
 * @property-read string|null $ifRedirect
 */
abstract class AStageDef extends AbsStageDef
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',

            'contextName' => '',
            'stageName' => '',
            'asIntent' => null,

            'events' => [],
            'ifRedirect' => null,
        ];
    }

    public function __get_events() : array
    {
        $events = $this->_data['events'] ?? [];
        $results = [];
        foreach ($events as $event => $action) {
            $results[$event] = $action;
        }

        return $results;
    }

    public function __set_events(string $name, array $events) : void
    {
        $results = [];
        foreach ($events as $event => $action) {
            $results[$event] = $action;
        }

        $this->_data[$name] = $results;
    }

    /*------- methods -------*/

    protected function fireEvent(Dialog $dialog) : ? Operator
    {
        foreach ($this->events as $event => $action) {
            if (!$dialog->isEvent($event)) {
                continue;
            }

            $operator = $dialog->container()->action($action);

            if (isset($operator)) {
                return $operator;
            }
        }

        return null;
    }

    protected function fireRedirect(Dialog $prev) : ? Operator
    {
        $redirect = $this->ifRedirect;

        if (isset($redirect)) {
            return $prev
                ->container()
                ->action($redirect, ['prev' => $prev]);
        }

        return null;
    }

}