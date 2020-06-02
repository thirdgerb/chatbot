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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\MindDef\EntityDef;
use Commune\Blueprint\Ghost\MindReg\SynonymReg;
use Commune\Blueprint\Ghost\MindMeta\EntityMeta;
use Commune\Blueprint\Ghost\MindMeta\SynonymMeta;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Support\WordSearch\Tree;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEntityDef implements EntityDef
{
    /**
     * @var EntityMeta
     */
    protected $meta;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * IEntityDef constructor.
     * @param EntityMeta $meta
     */
    public function __construct(EntityMeta $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param Meta $meta
     * @return Wrapper
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof EntityMeta) {
            throw new InvalidArgumentException('meta should be subclass of '. EntityMeta::class);
        }

        return new static($meta);
    }

    protected function getTree(SynonymReg $reg) : Tree
    {
        if (isset($this->tree)) {
            return $this->tree;
        }

        $synonyms = $this->getSynonymNames();

        if (!empty($synonyms)) {
            $this->prepareSynonyms($synonyms, $reg);
        }

        $values = $this->getValues();

        $keywords = [];
        foreach ($values as $value) {
            $keywords[strval($value)] = strval($value);
        }

        foreach ($synonyms as $synonym) {
            $def = $reg->getDef($synonym);
            foreach ($def->getValues() as $alias) {
                $key = strval($alias);
                $keywords[$key] = strval($synonym);
            }
        }

        return $this->tree = new Tree($keywords);
    }


    public function match(string $text, SynonymReg $reg): array
    {
        $tree = $this->getTree($reg);
        $matches = $tree->search($text);
        return array_keys($matches);
    }

    protected function prepareSynonyms(array $synonyms, SynonymReg $reg) : void
    {
        foreach ($synonyms as $value) {
            if (!$reg->hasDef($value)) {
                $meta = new SynonymMeta([
                    'name' => $value,
                    'synonyms' => [$value]
                ]);
                $reg->registerDef($meta->toWrapper(), true);
            }
        }
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


    public function getValues(): array
    {
        return $this->meta->values;
    }

    public function getBlacklist(): array
    {
        return $this->meta->blacklist;
    }

    public function getSynonymNames(): array
    {
        return $this->meta->synonyms;
    }

    public function toMeta(): Meta
    {
        return $this->meta;
    }


}