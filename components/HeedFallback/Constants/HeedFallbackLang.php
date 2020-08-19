<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Constants;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HeedFallbackLang
{
    const PLEASE_TEACH_ME = 'heed.fallback.teachMe';

    /**
     * @param {batchId}
     */
    const TEACH_TASK_NOT_FOUND = 'heed.fallback.teach.notFound';

    const CHAT_MODULE_NOT_FOUND = 'heed.fallback.chat.notFound';

    /**
     * @param {await}
     * @param {context}
     * @param {stage}
     * @param {text}
     * @param {matched}
     */
    const FALLBACK_SCENE_BRIEF = 'heed.fallback.scene.brief';


    const REQUIRE_OPERATION = 'heed.fallback.require.operation';

    const REQUIRE_INTENT_NAME = 'heed.fallback.require.intentName';

    const REQUIRE_DIRECT_REPLY = 'heed.fallback.require.reply';

    /**
     * @param {reply}
     */
    const SIMPLE_CHAT_REPLY = 'heed.fallback.chat.reply';

    const SIMPLE_CHAT_CONFIRM = 'heed.fallback.chat.confirm';


    /**
     * @param {count}
     */
    const TOTAL_TASKS = 'heed.fallback.task.total';

    const CONFIRM_START_TASK = 'heed.fallback.task.start';

    const TASK_DISAPPEAR = 'heed.fallback.task.disappear';
}