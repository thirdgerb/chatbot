<?php

/**
 * Class Waiter
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant;


use Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Welcome;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Directing\Location;
use Commune\Chatbot\Framework\Directing\SpecialLocations\Guesting;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Waiter extends ContextCfg
{
    const SCOPE = [Scope::SESSION];

    const DEPENDS = [
        'customer' => [Customer::class],
        'orders' => [Orders::class],
    ];

    const DATA = [
        'ordering' => null,
        'ordered' => false,
    ];

    public function prepared(Context $context)
    {
        $context->info('欢迎光临!'.$context['customer']['call']);
        $context->info("请问我有什么可以为您做的?");
    }

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action()
                ->info("不好意思, 我不明白您的意思");

        $route->hearsCommand('menu')
            ->exactly('点菜')
            ->exactly('点餐')
            ->exactly('菜单')
            ->action()
            ->callSelf('askToOrder');

        $route->name('choseType')
            ->action()
                ->call(function(Context $context, Intent $intent) {
                    $message = $intent['result'];
                    switch ($message) {
                        case '面条':
                            $context['ordering'] = 'noodles';
                            return null;
                        case '包子':
                            $context['ordering'] = 'bun';
                            return null;
                        default:
                            $context->info('对不起, 您点的不在我们菜单上呢');
                            return $context->callConfigMethod('askToOrder', $intent);
                    }
                })
            ->redirect(['ordering' => 'bun'])
                ->guest(Bun::class, [], 'checkOrder')
            ->redirect(['ordering' => 'noodles'])
                ->guest(Noodles::class, [], 'checkOrder');

        $route->name('checkOrder')
            ->action()
                ->call(function(Context $context, Intent $intent) {
                    $name = $intent['name'];
                    $tags = $intent['tags'];
                    $num = $intent['num'];
                    $desc = $intent['desc'];
                    $context->info("您点了 $desc");

                    $selected = $context['orders']['selected'];
                    $selected[] = new Selected($name, $num, $tags);
                    $context['orders']['selected'] = $selected;
                    $context['ordering'] = null;
                })
            ->redirect()
                ->confirm(
                    'continueOrder',
                    '需要继续点菜吗?',
                    '是'
                );


        $route->name('continueOrder')
            ->action()
                ->call(function(Context $context, Intent $intent) {
                    $confirmation = $intent['result'];

                    if ($confirmation) {
                        return $context->callConfigMethod('askToOrder', $intent);
                    } else {
                        return $context->callConfigMethod('confirmOrders', $intent);
                    }

                });


        $route->name('orderConfirmation')
            ->action()
                ->call(function(Context $context, Intent $intent) {
                    $confirmation = $intent['result'];

                    if ($confirmation) {
                        $context['ordered'] = true;
                        $context->info('好的, 您的菜品正在制作中, 请稍候');
                    } else {
                        $context['orders']['selected'] = [];
                        $context->info('明白了, 需要的话可以重新点餐');
                        $context['ordered'] = false;
                    }

                });

        $route->hearsCommand('ordered')
            ->exactly('点了什么')
            ->exactly('点了啥')
            ->action()
                ->call(function (Context $context, Intent $intent) {

                    if (empty($context['orders']['selected'])) {
                        $context->info("不好意思, 您还什么都没点呢");
                    } else {
                        $selects = $context['orders']['selected'];

                        $context->info("您点了: ");
                        foreach ($selects as $selected) {
                            if ($selected instanceof Selected) {
                                $str = $selected->toString();
                                $context->info($str);
                            }
                        }
                    }

                });

        $route->hearsCommand('leave')
            ->exactly('走了')
            ->action()
                ->info('谢谢您的惠顾, 期待您下次再来光临')
                ->info("\n")
            ->redirect()
                ->to(Welcome::class);
    }


    public function askToOrder(Context $context, Intent $intent) : Location
    {
        return $context->choose(
            'choseType',
            '这是我们的菜单, 您看看有什么想要的?',
            [
                '面条',
                '包子'
            ]
        );
    }

    public function confirmOrders(Context $context, Intent $intent) : Location
    {
        $context->info('您点的菜如下: ');

        foreach($context['orders']['selected'] as $selected) {
            if ($selected instanceof Selected) {
                $context->info($selected->toString());
            }
        }

        return $context->confirm(
            'orderConfirmation',
            '请问点的菜单确认了吗?',
            '是'
        );
    }

}