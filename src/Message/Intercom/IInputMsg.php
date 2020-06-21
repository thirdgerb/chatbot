<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Intercom;

use Commune\Message\Abstracted\IComprehension;
use Commune\Message\Host\Convo\IText;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $scene
 * @property array $env
 *
 * @property string $shellId  不可为空.
 * @property string $sessionId  会话Id, 为空则是 guestId
 * @property string $convoId    多轮会话的 ID. 允许为空. 除非客户端有指定的 conversation.
 * @property string $guestId    用户的ID. 不可以为空.
 * @property string $guestName  用户的姓名. 可以为空.
 *
 * @property string $messageId  为空则自动生成.
 * @property HostMsg $message   输入消息. 不可以为空.
 * @property int $createdAt     创建时间.
 * @property int $deliverAt     发送时间. 默认为0.
 *
 * @property Comprehension $comprehension   对输入消息的抽象理解. 允许为空.
 */
class IInputMsg extends AIntercomMsg implements InputMsg
{

    /**
     * 由于定义的 HostMsg 是 interface, 所以任何时候都要保留该对象的结构.
     * @var bool
     */
    protected $transferNoEmptyRelations = false;

    /**
     * 自身为空数据时, 不需要保留结构.
     * @var bool
     */
    protected $transferNoEmptyData = true;

    public static function stub(): array
    {
        return [
            // 不可为空.
            'shellId' => '',

            // 传入值允许为空, 则会用 guestId 替代.
            'sessionId' => '',

            // 通常为空. 除非是客户端传来一个明确的 conversationId
            'convoId' => '',

            // 不可为空.
            'guestId' => '',

            // 允许为空. 有的客户端没有 guestName
            'guestName' => '',

            // 如果为空, 会自动生成一个 uuid
            'messageId' => '',

            // 默认的消息
            'message' => new IText(),

            // 决定消息发送的时间点.
            'deliverAt' => 0,

            'createdAt' => time(),

            'scene' => '',

            'env' => [],

            // 可以为空. 除非客户端传来时已经带有理解信息了.
            'comprehension' => new IComprehension(),
        ];
    }

    public static function relations(): array
    {
        return [
            'comprehension' => IComprehension::class,
            'message' => HostMsg::class,
        ];
    }


    public function isInvalid(): ? string
    {
        return TypeUtils::requireFields(
            $this->_data,
            ['guestId', 'messageId', 'shellName', 'message']
        );
    }


    public function getScene(): string
    {
        return $this->scene;
    }

    public function getEnv(): array
    {
        return $this->env;
    }

    public function getComprehension(): Comprehension
    {
        return $this->comprehension;
    }

    /**
     * @param HostMsg $message
     * @param int $deliverAt
     * @param string|null $shellName
     * @param string|null $sessionId
     * @param string|null $guestId
     * @param string|null $messageId
     * @return OutputMsg
     */
    public function output(
        HostMsg $message,
        int $deliverAt = 0,
        string $shellName = null,
        string $sessionId = null,
        string $guestId = null,
        string $messageId = null
    ): OutputMsg
    {
        $shellName = empty($shellName) ? $this->getShellId() : $shellName;

        return new IOutputMsg([
            'messageId' => empty($messageId) ? $this->createUuId() : $messageId,
            'shellName' => $shellName,
            'traceId' => $this->getTraceId(),
            'sessionId' => empty($sessionId) ? $this->getSessionId() : $sessionId,
            'convoId' => $this->getConvoId(),
            'guestId' => $guestId ?? $this->getCreatorId(),
            'guestName' => $this->getCreatorName(),
            'message' => $message,
            'deliverAt' => $deliverAt,
        ]);
    }

    public function getShellId(): string
    {
        return $this->shellId;
    }

    public function getTraceId(): string
    {
        return $this->getMessageId();
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }


    public function setSceneId(string $sceneId): void
    {
        $this->scene = $sceneId;
    }

    public function asBareIntercom(): InputMsg
    {
        $data = $this->toArray();
        unset($data['comprehension']);
        unset($data['sceneId']);
        unset($data['env']);

        return static::create($data);
    }


}