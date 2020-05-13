<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\Defs;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Mind\Defs\MemoryDef;
use Commune\Ghost\Memory\IRecollection;
use Commune\Ghost\Mind\Metas\MemoryMeta;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMemoryDef implements MemoryDef
{

    /**
     * @var MemoryMeta
     */
    protected $meta;

    /**
     * IMemoryDef constructor.
     * @param MemoryMeta $meta
     */
    public function __construct(MemoryMeta $meta)
    {
        $this->meta = $meta;
    }


    public function getName(): string
    {
        return $this->meta->name;
    }

    public function getTitle(): string
    {
        return $this->meta->title;
    }

    public function getDescription(): string
    {
        return $this->meta->desc;
    }

    public function getScopes(): array
    {
        return $this->meta->scopes;
    }

    public function getDefaults(): array
    {
        return $this->meta->defaults;
    }

    public function isLongTerm(): bool
    {
        $scopes = $this->getScopes();
        return !empty($scopes);
    }


    public function recall(Cloner $cloner): Recollection
    {
        $id = $cloner->scope->makeScopeId(
            $name = $this->getName(),
            $this->getScopes()
        );

        $memory = $cloner->runtime->findMemory(
            $id,
            $longTerm = $this->isLongTerm(),
            $this->getDefaults()
        );

        return new IRecollection(
            $id,
            $name,
            $longTerm,
            $memory,
            $cloner
        );
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public static function wrap(Meta $meta): Wrapper
    {
        if (!$meta instanceof MemoryMeta) {
            throw new InvalidArgumentException(
                __METHOD__,
                'meta',
                'meta should be subclass of ' . MemoryMeta::class
            );
        }

        return new static($meta);
    }


}