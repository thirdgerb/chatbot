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

use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Ghost\Context\Prototype\IContextDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICodeContextDef extends IContextDef
{
    protected $contextClass;

    public function __construct(string $contextClass, ContextMeta $meta = null)
    {
        $creator = new CodeDefCreator($contextClass);

        $option = $creator->getCodeContextOption();
        $config = isset($meta) ? $meta->config : [];

        parent::__construct([
            'name' => isset($meta) ? $meta->name : $creator->getContextName(),
            'title' => isset($meta) ? $meta->title : $option->title,
            'desc' => isset($meta) ? $meta->desc : $option->desc,

            'contextWrapper' => $contextClass,

            'priority' => $config['priority'] ?? $option->priority,
            'asIntent' => $config['asIntent'] ?? $creator->getContextIntentInfo(),

            'queryParams' => $config['queryParams'] ?? $option->queryParams,

            'memoryScopes' => $config['memoryScopes'] ?? $option->memoryScopes,
            'memoryParams' => $config['memoryParams'] ?? $option->memoryParams,

            'dependingNames' => $config['dependingNames'] ?? $option->dependingNames,
            'entityNames' => $config['entityNames'] ?? $option->entityNames,

            'comprehendPipes' => $config['comprehendPipes'] ?? $option->comprehendPipes,

            'onCancel' => $config['onCancel'] ?? $option->onCancel,
            'onQuit' => $config['onQuit'] ?? $option->onQuit,
            'stageRoutes' => $config['stageRoutes'] ?? $option->stageRoutes,
            'contextRoutes' => $config['contextRoutes'] ?? $option->contextRoutes,

            'stages' => $creator->getPredefinedStageMetas(),
        ]);
    }
}