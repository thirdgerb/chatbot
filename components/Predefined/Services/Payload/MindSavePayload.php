<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Services\Payload;

use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $metaName
 * @property-read array $metaData
 * @property-read bool|null $force
 */
class MindSavePayload extends AStruct
{
    public static function stub(): array
    {
        return [
            'metaName' => '',
            'metaData' => [],
            'force' => null
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $metaName = $data['metaName'] ?? '';
        if (!is_a($metaName, DefMeta::class, TRUE)) {
            return 'invalid meta name';
        }

        if (empty($data['metaData'])) {
            return 'meta data is empty';
        }

        return parent::validate($data);
    }

    public function toMeta() : DefMeta
    {
        return call_user_func(
            [
                $this->metaName,
                DefMeta::FUNC_CREATE
            ],
            $this->metaData
        );
    }

    public static function relations(): array
    {
        return [];
    }


}