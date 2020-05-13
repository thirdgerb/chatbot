<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\Metas;

use Commune\Blueprint\Ghost\Mind\Defs\SynonymDef;
use Commune\Ghost\Mind\Defs\ISynonymDef;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Wrapper;
use Commune\Support\Utils\StringUtils;


/**
 * 同义词的元数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string[] $synonyms
 *
 */
class SynonymMeta extends AbsOption implements DefMeta
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'synonyms' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        if (!StringUtils::isNotEmptyStr($data['name'] ?? null)) {
            return 'name is required';
        }

        if (empty($data['synonyms'])) {
            return 'synonyms should not be empty';
        }

        return parent::validate($data);
    }

    /**
     * @return SynonymDef
     */
    public function getWrapper(): Wrapper
    {
        return new ISynonymDef($this);
    }


}