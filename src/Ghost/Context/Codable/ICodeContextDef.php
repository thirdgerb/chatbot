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

use Commune\Blueprint\Ghost\Context\CodeContext;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\IContextDef;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICodeContextDef extends IContextDef
{
    protected $contextClass;

    public function __construct(string $contextClass, ContextMeta $meta = null)
    {
        $this->contextClass = $contextClass;
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
            'auth' => $config['auth'] ?? $option->auth,
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
            'firstStage' => CodeContext::FIRST_STAGE
        ]);
        unset($creator);
    }

    public function toMeta(): Meta
    {
        $config = $this->toArray();
        unset($config['name']);
        unset($config['title']);
        unset($config['desc']);

        return new ContextMeta([
            'name' => $this->name,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => $this->contextClass,
            'config' => $config
        ]);
    }

    /**
     * @param Meta $meta
     * @return ContextDef
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof ContextMeta) {
            throw new InvalidArgumentException(
                __METHOD__
                . ' only accept meta of subclass ' . ContextMeta::class
            );
        }

        $className = $meta->wrapper;
        return new static($className, $meta);
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return call_user_func([$this->contextClass, CodeContext::FUNC_REDIRECT], $prev, $current);
    }
}