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



    /**
     * @param {text}
     */
    const REQUIRE_INTENT_NAME = 'heed.fallback.require.intentName';

    const REQUIRE_SEARCH_INTENT = 'heed.fallback.search.intentName';

    const REQUIRE_TRANS_REPLY = 'heed.fallback.require.transReply';

    /**
     * @param {intent}
     */
    const REQUIRE_INTENT_CREATE = 'heed.fallback.require.createIntent';


    /**
     * @param {intent}
     */
    const SELECTED_INTENT_NOT_FOUND = 'heed.fallback.intent.notFound';

    /**
     * @param {selected}
     */
    const CONFIRM_INTENT = 'heed.fallback.intent.confirm';


    const STRATEGY_CHOOSE_SCOPE = 'heed.fallback.strategy.scope';

    const STRATEGY_SCOPE_CONTEXT = 'heed.fallback.strategy.scope.context';
    const STRATEGY_SCOPE_STAGE = 'heed.fallback.strategy.scope.stage';
    const STRATEGY_SCOPE_INTENT = 'heed.fallback.strategy.scope.intent';


    /**
     * @param {id}
     */
    const STRATEGY_CHOOSE = 'heed.fallback.strategy.choose';

    const REPLY_IS_PREPARING = 'heed.fallback.reply.preparing';

    /**
     * @param {text}
     */
    const STRATEGY_LEARNED = 'heed.fallback.strategy.learned';

    /**
     * @param {intent}
     * @param {await}
     */
    const STRATEGY_EXISTS = 'heed.fallback.strategy.exists';
}