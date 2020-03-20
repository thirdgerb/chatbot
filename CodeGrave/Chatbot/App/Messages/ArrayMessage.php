<?php


namespace Commune\Chatbot\App\Messages;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\TransformedMsg;
use Commune\Chatbot\Framework\Messages\AbsConvoMsg;
use Commune\Chatbot\OOHost\Dialogue\Hearing\HearingHandler;

/**
 * 数组形式的消息.
 * 将原消息抽取了关键信息后, 转化成了一个数组
 *
 * @see HearingHandler  Hearing::pregMatch
 */
class ArrayMessage extends AbsConvoMsg implements \ArrayAccess, TransformedMsg
{

    /**
     * @var Message
     */
    protected $origin;

    /**
     * @var array
     */
    protected $data;

    /**
     * ArrayMessage constructor.
     * @param Message $origin
     * @param array $array
     */
    public function __construct(Message $origin, array $array)
    {
        $this->origin = $origin;
        $this->data = $array;
        parent::__construct();
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['origin', 'data']);
    }


    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function getText(): string
    {
        return $this->getOriginMessage()->getText();
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function getOriginMessage(): Message
    {
        return $this->origin;
    }

    public static function mock()
    {
        return new static(Text::mock(), ['a' => 1, 'b' => 2]);
    }

}