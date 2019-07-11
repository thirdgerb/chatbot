<?php

/**
 * Class Message
 * @package Commune\Chatbot\Framework\Message
 */

namespace Commune\Chatbot\Framework\Messages;


use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Tags\Transformed;
use Commune\Chatbot\Framework\Utils\CommandUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Chatbot\Blueprint\Message\Message as Contract;

/**
 * 所有消息的基础抽象.
 *
 * Class Message
 * @package Commune\Chatbot\Framework\Message
 */
abstract class AbsMessage implements Contract
{
    use ArrayAbleToJson;

    const USER_COMMAND_MARK = '#';

    /**
     * @var Carbon
     */
    protected $_createdAt;

    /**
     * @var Carbon|null
     */
    protected $_deliverAt;

    protected $_cmdText;

    /**
     * AbsMessage constructor.
     * @param Carbon $createdAt
     */
    public function __construct(Carbon $createdAt = null)
    {
        $this->_createdAt = $createdAt ?? new Carbon();
    }


    public function getMessageType(): string
    {
        return static::class;
    }

    /**
     * 默认的数据结构.
     *
     * @return array
     */
    public function toArray() : array
    {
        $data =  [
            'type' => $this->getMessageType(),
            'data' => $this->toMessageData(),
            'createdAt' => $this->getCreatedAt()
        ];

        if ($this instanceof Transformed) {
            $data['origin'] = $this->getOriginMessage()->toMessageData();
        }
        return $data;
    }

    /**
     * 去掉了多余格式后的文本.
     * @return string
     */
    public function getTrimmedText() : string
    {
        $text = $this->getText();

        // 去掉全角符号, 降低复杂性.
        $text = CommandUtils::sbc2dbc($text);

        // 系统默认的空字符.
        if ($text === '.' || $text === ',' || $text === ';') {
            return '';
        }

        return trim($text);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->_createdAt ?? $this->_createdAt = new Carbon();
    }


    public function namesAsDependency(): array
    {
        return [
            'message',
            Message::class,
            static::class,
        ];
    }

    public function getCmdText(): ? string
    {
        return $this->_cmdText
            ?? $this->_cmdText = CommandUtils::getCommandStr(
                $this->getTrimmedText(),
                // 确保 commandUtils 会使用 user command mark
                null
            );
    }

    public function setCmdText(string $text = null): void
    {
        $this->_cmdText = $text;
    }

    public function __toString()
    {
        return $this->getText();
    }

    public function getDeliverAt(): ? Carbon
    {
        return $this->_deliverAt;
    }

    public function deliverAt(Carbon $carbon): Message
    {
        $this->_deliverAt = $carbon;
        return $this;
    }


}