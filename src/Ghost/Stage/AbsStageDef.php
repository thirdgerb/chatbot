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
use Commune\Blueprint\Ghost\MindDef\AliasesForStage;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read IntentMeta $asIntent
 *
 * @property-read string $title
 * @property-read string $desc
 *
 * @property-read string[] $events
 * @property-read string|null $ifRedirect
 */
abstract class AbsStageDef extends AbsOption implements StageDef
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
            'asIntent' => [],

            'events' => [],
            'ifRedirect' => null,
        ];
    }

    public static function relations(): array
    {
        return [
            'asIntent' => IntentMeta::class,
        ];
    }

    public function _filter(array $data): void
    {
        $data['asIntent'] = IntentMeta::mergeStageInfo(
            $data['asIntent'] ?? [],
            $data['name'] ?? '',
            $data['title'] ?? '',
            $data['desc'] ?? ''
        );

        parent::_filter($data);
    }

    public function __get_events() : array
    {
        $events = $this->_data['events'] ?? [];
        $results = [];
        foreach ($events as $event => $action) {

            $event = AliasesForStage::getOriginFromAlias($event);
            $action = AliasesForStage::getOriginFromAlias($action);

            $results[$event] = $action;
        }

        return $results;
    }

    public function __set_events(string $name, array $events) : void
    {
        $results = [];
        foreach ($events as $event => $action) {
            $event = AliasesForStage::getAliasOfOrigin($event);
            $action = AliasesForStage::getAliasOfOrigin($action);
            $results[$event] = $action;
        }

        $this->_data[$name] = $results;
    }

    /*------- methods -------*/

    protected function fireEvent(Dialog $dialog) : ? Operator
    {
        foreach ($this->events as $event => $action) {

            $event = AliasesForStage::getOriginFromAlias($event);
            $action = AliasesForStage::getOriginFromAlias($action);

            if (!is_a($dialog, $event, TRUE)) {
                continue;
            }

            $operator = $dialog->caller()->action($action);

            if (isset($operator)) {
                return $operator;
            }
        }

        return null;
    }

    protected function fireRedirect(Dialog $prev, Dialog $current) : ? Operator
    {
        $redirect = $this->ifRedirect;

        if (isset($redirect)) {
            return $current
                ->caller()
                ->action($redirect, [
                    'prev' => $prev,
                    'current' => $current
                ]);
        }

        return null;
    }

    /*------- properties -------*/

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->desc;
    }

    public function getStageShortName(): string
    {
        return $this->stageName;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function isContextRoot(): bool
    {
        return $this->name === $this->contextName;
    }

    public function asIntentDef(): IntentDef
    {
        $config = $this->asIntent;
        if (empty($config['name'])) {
            $config['name'] = $this->name;
        }

        if (empty($config['title'])) {
            $config['title'] = $this->title;
        }

        if (empty($config['desc'])) {
            $config['desc'] = $this->desc;
        }

        return new IIntentDef(new IntentMeta($config));
    }

    /*------- wrapper -------*/

    public function getMeta(): Meta
    {
        $data = $this->toArray();

        $name = $data['name'] ?? '';
        unset($data['name']);

        $contextName = $data['contextName'] ?? '';
        unset($data['contextName']);

        $title = $data['title'] ?? '';
        unset($data['title']);

        $desc = $data['desc'] ?? '';
        unset($data['desc']);


        return new StageMeta([
            'name' => $name,
            'contextName' => $contextName,
            'title' => $title,
            'desc' => $desc,
            'wrapper' => static::class,
            'config' => [],
        ]);
    }

    public static function wrap(Meta $meta): Wrapper
    {
        if (!$meta instanceof StageMeta) {
            throw new InvalidArgumentException(
                'only accept subclass of ' . StageMeta::class
            );
        }

        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['contextName'] = $meta->contextName;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;

        return new static($config);
    }


}