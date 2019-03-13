<?php

/**
 * Class Noodles
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Context\Predefined\Answer;
use Commune\Chatbot\Framework\Context\Predefined\Choice;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Bun extends ContextCfg
{
    const SCOPE = [Scope::MESSAGE];

    const DATA = [
        'name' => '包子'
    ];

    const DEPENDS = [
        'type' => [
            Choice::class,
            [
                'question' => '请问您想要哪种馅的包子?',
                'choices' => [
                    '猪肉韭菜',
                    '青椒',
                    '蟑螂'
                ]
            ]
        ],
        'num' => [
            Answer::class,
            [
                'question' => '请问您需要多少个?',
                'default' => 8,
            ]
        ]
    ];

    public function routing(DialogRoute $route)
    {
        $route->prepared()
            ->redirect()
                ->intended();
    }

    public function fetchEntities(Context $context)
    {
        return [
            'name' => $name = $context['name'],
            'tags' => [ $type = $context['type']['result'] .'馅' ],
            'num' => $num = $context['num']['result'],
            'desc' => "$num 个 $name, $type"
        ];
    }


}