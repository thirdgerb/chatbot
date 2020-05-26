<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;
use Commune\Ghost\Context\IParamDefCollection;
use Commune\Ghost\Memory\IRecollection;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;


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
     * @var ParamDefCollection
     */
    protected $params;

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

    public function getParams(): ParamDefCollection
    {
        return $this->params
            ?? $this->params = new IParamDefCollection(
                array_map(function(ParamOption $option) {
                    return new IParamDef($option);
                }, $this->meta->params)
            );
    }


    public function getDefaults(): array
    {
        return $this->getParams()->getDefaultValues();
    }

    public function isLongTerm(): bool
    {
        $scopes = $this->getScopes();
        return !empty($scopes);
    }


    public function recall(Cloner $cloner, string $id = null): Recollection
    {
        $name = $this->getName();
        $id = $id ?? $cloner->scope->makeScopeId(
                $name,
                $this->getScopes()
            );

        $memoryDef = $cloner->mind->memoryReg()->getDef($name);

        return new IRecollection(
            $id,
            $memoryDef,
            $cloner
        );
    }

    public function makeScopeId(Cloner $cloner): string
    {
        return $cloner->scope->makeScopeId(
            $this->getName(),
            $this->getScopes()
        );
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public static function wrap(Meta $meta): Wrapper
    {
        if (!$meta instanceof MemoryMeta) {
            throw new InvalidArgumentException('meta should be subclass of ' . MemoryMeta::class);
        }

        return new static($meta);
    }


}