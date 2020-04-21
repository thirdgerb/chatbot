<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;

use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $type                      消息类型, 对应 Message::getType()
 * @property-read array $attrs                      消息属性. 每个都对应 getter 方法
 * @property-read array|Transfer[][]|Transfer[] $relations  关联对象. 都可以反序列化为 Meta, 进一步变成 Message. 也是一种 Fractal
 * @property-read string[] $protocals               消息实现的协议.
 */
class ITransfer implements Transfer
{
    use ArrayAbleToJson;

    /**
     * @var array
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }


    public function toArray(): array
    {
        $data = $this->data;
        $relations = $data['relations'];

        if (empty($relations)) {
            return $data;
        }

        foreach ($relations as $name => $values) {

            if ($values instanceof Transfer) {
                $relations[$name] = $values->toArray();

            } elseif (is_array($values)) {
                foreach ($values as $index => $transfer) {
                    $relations[$name][$index] = $transfer->toArray();
                }
            }
        }

        $data['relations'] = $relations;
        return $data;
    }

    public function toMessage(): Message
    {
        $type = $this->type;
        $className = StringUtils::dotToNamespaceSlash($type);

        if (!is_a($className, Message::class, TRUE)) {
            throw new MessageNotDefinedException($className);
        }

        $relations = $this->relations ?? [];

        //todo
    }

    public function __destruct()
    {
        $this->data = [];
    }

}