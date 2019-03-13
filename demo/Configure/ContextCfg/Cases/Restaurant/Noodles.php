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

class Noodles extends ContextCfg
{
    const SCOPE = [Scope::MESSAGE];

    const DATA = [
        'name' => '面条'
    ];

    const DEPENDS = [
        'type' => [
            Choice::class,
            [
                'question' => '请问您想要什么卤子?',
                'choices' => [
                    '西红柿鸡蛋',
                    '青椒炒肉',
                    '胡椒炒花椒'
                ]
            ]
        ],
        'spice' => [
            Choice::class,
            [
                'question' => '要加辣吗?',
                'choices' => [
                    '不辣',
                    '微辣',
                    '中辣',
                    '超级辣',
                ]
            ]
        ],
        'weight' => [
            Answer::class,
            [
                'question' => '请问您需要几两面?',
                'default' => 2,
            ]
        ],
        'num' => [
            Answer::class,
            [
                'question' => '需要几份?',
                'default' => 1,
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
            'tags' => $tags = [
                $context['type'],
                $context['spice'],
                $context['weight'] .'两',
            ],
            'num' => $num = $context['num']['result'],
            'desc' => "$num 个 $name, " . implode($tags, ' ')
        ];
    }


}