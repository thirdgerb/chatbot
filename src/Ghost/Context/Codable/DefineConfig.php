<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefineConfig
{
    const DEFINE_CONFIG_FUNC = '__config';

    const KEY_PRIORITY = 'priority';
    const KEY_SCOPES = 'scopes';
    const KEY_TITLE = 'title';
    const KEY_DESC = 'desc';
    const KEY_COMPREHEND_PIPES = 'comprehendPipes';
    const KEY_ON_CANCEL = 'onCancel';
    const KEY_ON_QUIT = 'onQuit';
    const KEY_STAGE_ROUTES = 'stageRoutes';
    const KEY_CONTEXT_ROUTES = 'contextRoutes';

    /**
     * @return array
     *
     * [
     *  'priority' => 0,
     *  'scopes' => [ClonerScope::CLONE_ID],
     *  'title' => '',
     *  'desc' => '',
     *  'comprehendPipes' => [],
     *  'onCancel' => '',
     *  'onQuit' => '',
     *  'stageRoutes' => [],
     *  'contextRoutes' => [],
     * ]
     */
    public static function __config() : array;
}