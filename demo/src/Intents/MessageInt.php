<?php

namespace Commune\Demo\Intents;

use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 模拟消息类意图
 */
class MessageInt extends AbsCmdIntent
{
    // 定义命令的匹配逻辑
    const SIGNATURE = 'testMessage';

    const DESCRIPTION = '模拟消息类意图';

    // 定义正则的匹配逻辑
    const REGEX = [
        // 定义正则匹配规则
        ['/^hello|你好$/'],
        // 定义更多正则匹配. 故意定义了有瑕疵的正则, hiiiii 也能匹配到
        ['/^hi/'],
    ];


    public static function getContextName(): string
    {
        return 'demo.lesions.message';
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return null;
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->dialog->fulfill();
    }


}