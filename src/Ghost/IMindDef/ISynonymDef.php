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
use Commune\Blueprint\Ghost\MindDef\SynonymDef;
use Commune\Blueprint\Ghost\MindMeta\SynonymMeta;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ISynonymDef implements SynonymDef
{
    /**
     * @var SynonymMeta
     */
    protected $meta;

    /**
     * ISynonymDef constructor.
     * @param SynonymMeta $meta
     */
    public function __construct(SynonymMeta $meta)
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

    public function getValues(): array
    {
        return $this->meta->synonyms;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public static function wrap(Meta $meta): Wrapper
    {
        if (!$meta instanceof SynonymMeta) {
            throw new InvalidArgumentException(
                __METHOD__,
                'meta',
                "meta should be subclass of ". SynonymMeta::class
            );
        }
        return new static($meta);
    }


}