<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Abstracted;

use Commune\Framework\Blueprint\Abstracted\SoundLike;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ISoundLike implements SoundLike
{
    use ArrayAbleToJson;

    public $soundLikes = [];

    public function toArray(): array
    {
        return [
            'soundLikes' => $this->soundLikes
        ];
    }

    public function addSoundLike(string $lang, string $soundLike): void
    {
        $this->soundLikes[$lang] = $soundLike;
    }

    public function getSoundLike(string $lang): ? string
    {
        return $this->soundLikes[$lang] ?? null;
    }


}