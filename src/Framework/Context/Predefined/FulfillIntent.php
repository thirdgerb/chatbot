<?php

/**
 * Class FulfillIntent
 * @package Commune\Chatbot\Framework\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;

use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Directing\Location;
use Commune\Chatbot\Framework\Intent\Predefined\ArrayIntent;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class FulfillIntent extends ContextCfg
{
    const SCOPE = [
        Scope::MESSAGE
    ];

    const DATA = [
        'entities' => [],
        'fulfilled' => false,
        'callbackField' => '',
    ];

    const PROPS = [
        'intentId' => '',
        'intentEntities' => [],
        'questions' => [
            //name => [ask, question, default]
            //name => [choose, question, choices, default]
            //name => [confirm, question, default]
        ],
    ];


    public function creating(Context $context)
    {
        $context['entities'] = $context['intentEntities'];
    }

    public function routing(DialogRoute $route)
    {
        $route->prepared()
            ->action()
            ->callSelf('checkFulfill');


        $route->name('questionFallback')
            ->action()
            ->callSelf('questionFallback')
                ->redirect(['fulfilled' => true])
                ->intended();
    }


    public function checkFulfill(Context $context, Intent $intent = null) : ? Location
    {
        $entities = $context['entities'];
        foreach ($context['questions'] as $field => $params) {
            if (!isset($entities[$field])) {
                $context['callbackField'] = $field;
                $type = array_shift($params);

                switch($type) {
                    case 'choose':
                        return $context->choose(
                            'questionFallback',
                            $params[0] ?? '请选择'.$field,
                            $params[1] ?? [],
                            $params[2] ??  0
                        );
                    case 'confirm' :
                        return $context->confirm(
                            'questionFallback',
                            $params[0] ?? '请确认'.$field,
                            $params[2] ?? 'yes'
                        );
                    case 'ask' :
                    default :
                        $q = $context->ask(
                            'questionFallback',
                            $params[0] ?? "请输入$field",
                            $params[2] ?? ''
                        );
                        return $q;

                }
            }
        }
        $context['fulfilled'] = true;
        return null;
    }


    public function questionFallback(Context $context, Intent $intent) : ? Location
    {
        $callback = $context['callbackField'];

        $entities = $context['entities'];
        if ($callback) {
            $entities[$callback] = $intent['result'];
            $context['entities'] = $entities;
            $context['callbackField'] = '';
        }

        return $this->checkFulfill($context);
    }

    public function toIntent(Context $context, Message $message): Intent
    {
        return new ArrayIntent(
            $context['intentId'],
            $message,
            $context['entities']
        );
    }


}