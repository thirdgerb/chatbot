<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Render;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $title
 * @property-read string $renderer
 */
class RendererOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',
            'renderer' => '',
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $stub = static::stub();
        foreach ($stub as $key => $val) {
            if (empty($data[$key])) {
                return "$key is required";
            }
        }

        return parent::validate($data);
    }

    public static function relations(): array
    {
        return [];
    }


}