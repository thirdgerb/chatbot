<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Protocals\Abstracted;
use Commune\Protocals\Comprehension;
use Commune\Support\Message\AbsMessage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property bool[] $handledBy
 */
class IComprehension extends AbsMessage implements Comprehension
{
    protected $transferNoEmptyRelations = true;

    public static function stub(): array
    {
        return [
            'choice' => [],
            'intention' => [],
            'reply' => [],
            'handledBy' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'choice' => IChoice::class,
            'intention' => IIntention::class,
            'reply' => IReply::class,
        ];
    }

    public function handledBy(string $comprehenderId, bool $succeed): void
    {
        $handledBy = $this->handledBy;
        $handledBy[$comprehenderId] = $succeed;
        $this->handledBy = $handledBy;
    }

    public function isHandledBy(string $comprehenderId): bool
    {
        $handledBy = $this->handledBy;
        return array_key_exists($comprehenderId, $handledBy);
    }

    public function isSucceedBy(string $comprehenderId): bool
    {
        return $this->handledBy[$comprehenderId] ?? false;
    }

    public function isEmpty(): bool
    {
        $obj = $this;
        foreach ($obj as $key => $value) {
            if ($value instanceof Abstracted && !$value->isEmpty()) {
                return false;
            }

            if (!empty($value)) {
                return false;
            }
        }

        return true;
    }


}