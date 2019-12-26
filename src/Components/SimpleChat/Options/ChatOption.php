<?php

namespace Commune\Components\SimpleChat\Options;

use Commune\Support\Option;

/**
 * 定义对一个意图的回复.
 *
 * @property-read string $intent 意图的名称.
 * @property-read string[] $examples 意图的例句. 如果例句不存在的地方, 会注册这些例句.
 * @property-read string[] $replies 回复的内容. 多个内容会随机挑选一个.
 */
class ChatOption extends Option
{
    const IDENTITY = 'intent';

    public static function stub(): array
    {
        return [
            'intent' => 'attitudes.greet',
            'examples' => [

            ],
            'replies' => [
                "您好~!",
                "hello!",
                "hi!"
            ],
        ];
    }

    protected function init(array $data): array
    {
        $intent = $data['intent'] ?? '';
        $data['intent'] = trim($intent);
        return parent::init($data);
    }

    public static function validate(array $data): ? string
    {
        if (empty($data['intent'])) {
            return 'intent name is required';
        }

        if (empty($data['replies'])) {
            return 'replies is required';
        }

        return null;
    }


}