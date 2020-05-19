<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\ContextMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $contextName       语境名称
 * @property string $contextId         语境Id
 * @property-read array $query
 * @property array $data               语境的数据.
 */
class IContextMsg extends AbsMessage implements ContextMsg
{
    public static function stub(): array
    {
        return [
            'contextName' => '',
            'contextId' => '',
            'query' => [],
            'data' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function toContext(Cloner $cloner): Context
    {
        $ucl = Ucl::create($cloner, $this->contextName, $this->query);
        $context = $cloner->getContext($ucl);
        $context->merge($this->data);
        return $context;
    }

    public function getContextId(): string
    {
        return $this->contextId;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getMemorableData(): array
    {
        return $this->data;
    }


    public function isBroadcasting(): bool
    {
        return true;
    }

    public function getLevel(): string
    {
        return HostMsg::INFO;
    }


    public function getText(): string
    {
        return Ucl::encodeUcl($this->contextName, '', $this->query);
    }

    public function isEmpty(): bool
    {
        return false;
    }


}