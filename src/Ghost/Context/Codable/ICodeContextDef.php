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
        $contextName = isset($meta) ? $meta->name : $creator->getContextName();

        $option = $creator->getCodeContextOption();
        $config = isset($meta) ? $meta->config : [];
        $contextAnnotation = $creator->getContextAnnotation();

        $stages = $creator->getMethodStageMetas();
        $depending = $creator->getDependingBuilder();

        parent::__construct([
            'name' => $contextName,
            'title' => isset($meta) ? $meta->title : $contextAnnotation->title,
            'desc' => isset($meta) ? $meta->desc : $contextAnnotation->desc,

            'contextWrapper' => $contextClass,

            'priority' => $config['priority'] ?? $option->priority,
            'asIntent' => $config['asIntent'] ?? $contextAnnotation->asIntentMeta($contextName),

            'queryNames' => $config['queryNames'] ?? $option->queryNames,

            'memoryScopes' => $config['memoryScopes'] ?? $option->memoryScopes,

            'memoryAttrs' => $config['memoryAttrs']
                    ?? $depending->attrs + $option->memoryAttrs,

            'dependingAttrs' => $config['dependingNames'] ?? array_keys($depending->attrs),

            'comprehendPipes' => $config['comprehendPipes'] ?? $option->comprehendPipes,

            'onCancel' => $config['onCancel'] ?? $option->onCancel,
            'onQuit' => $config['onQuit'] ?? $option->onQuit,
            'stageRoutes' => $config['stageRoutes'] ?? $option->stageRoutes,
            'contextRoutes' => $config['contextRoutes'] ?? $option->contextRoutes,

            'stages' => $stages + $depending->stages,
        ]);

        unset($creator);
    }
}