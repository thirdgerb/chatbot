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

use Commune\Support\Babel\BabelSerializable;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Struct\AStruct;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsMessage extends AStruct implements Message, Injectable
{
    use TInjectable;


    /**
     * 默认的 Validate 基于反射来实现.
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data): ? string
    {
    }

    public function toTransferArr(): array
    {
        $data = $this->toArray();
        $relations = [];
        foreach (static::getRelationNames() as $name) {
            $relations[$name] = $data[$name] ?? [];
            unset($data[$name]);
        }

        $result = ['attrs' => $data];
        if (!empty($relations)) {
            $result['relations'] = $relations;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return BabelSerializable|null
     */
    public static function fromTransferArr(array $data): ? BabelSerializable
    {
        $info = $data['attrs'] ?? [];
        $relations = $data['relations'] ?? [];
        return static::create($info + $relations);
    }

    public static function getTransferId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    public function isProtocal(string $protocalName): bool
    {
       return class_exists($protocalName)
           && is_a($protocalName, Protocal::class, TRUE)
           && is_a($this, $protocalName, TRUE);
    }

    public function toProtocal(string $protocalName): ? Protocal
    {
        if ($this instanceof Protocal && $this->isProtocal($protocalName)) {
            return $this;
        }
        return null;
    }

    final public function getProtocals(): array
    {
        return static::getInterfacesOf(Protocal::class);
    }

    final public function getInterfaces(): array
    {
        return static::getInterfacesOf(Message::class);
    }
}