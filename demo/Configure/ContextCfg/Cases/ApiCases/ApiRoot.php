<?php

/**
 * Class ApiRoot
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\ApiCases
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\ApiCases;


use Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Welcome;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Choose;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class ApiRoot extends ContextCfg
{
    const SCOPE = [Scope::SESSION];

    public function creating(Context $context)
    {
        $context->info('您进入了 api 测试的语境.');
    }

    public function prepared(Context $context)
    {
        $context->info('输入 "back" 可返回 ');

        $context->reply(new Choose(
                '请选择可用的测试用例',
                [
                    'myip' => '测试用system直接调用ifconfig, 并展示结果.',
                    'ip' => '调用淘宝接口, 查询ip地址'
                ]
            )
        );
    }

    public function routing(DialogRoute $route)
    {
        $route->hearsCommand('myip')
            ->action()
            ->call(function(Context $context, Intent $intent){
                $lines = [];
                exec('ifconfig',$lines ,$returnr);

                $context->info("返回code $returnr, 以下是内容: ");
                foreach ($lines as $line) {
                    $context->info($line);
                }
            });

        $route->hearsCommand('time {when : 想查询的事件, 默认为当前时间}')
            ->action()
                ->call(function(Context $context, Intent $intent){
                    $when = $intent['when'];

                    $when = empty($when) ? '' : $when;

                    $stamp = strtotime($when);

                    $context->info('您查询的时间是: ' . date('Y-m-d H:i:s', $stamp));
                });

        $route->hearsCommand('ip {ip=8.8.8.8 : 您的IP地址, 用于查询所在城市}')
            ->action()
                ->call(function(Context $context, Intent $intent){

                    $context->info('您输入的ip : '. $intent['ip']);

                    $pattern = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
                    if (!preg_match($pattern, $ip = trim($intent['ip'])) ) {
                        $context->info('这个IP地址似乎不合法哦, 没通过正则检查');
                        return null;
                    }


                    $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=', $ip);
                    $json = json_decode($data, true);

                    if (empty($json)) {
                        $context->info('没有查询结果. 再试试?');
                    } elseif(
                        isset($json['code'])
                        && $json['code'] == 0
                        && isset($json['data'])
                        && is_array($json['data'])
                    ) {
                        $context->info("结果为: ");

                        $context->info(json_encode($json['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                    } else {
                        $context->info('似乎返回结果不正确');
                    }
                });

        $route->hears('back')
            ->action()
                ->info('返回测试菜单')
            ->redirect()
                ->to(Welcome::class);

        $route->fallback()
            ->action()
                ->info('sorry, 无法理解意图')
            ->redirect()
                ->restart();
    }


}