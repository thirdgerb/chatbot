<?php


namespace Commune\Demo\App\Components\CustomService\Intents;


use Commune\Chatbot\App\Intents\ActionIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property string $account
 * @property string $order
 */
class ReturnDeviceNotReceived extends ActionIntent
{
    const SIGNATURE = 'deviceNotReceived
        {account : 您好，请您提供一下您的注册账号，我帮您查询下您的订单情况}
        {order : 请问您退回设备的快递单号还有留存吗？方便提供一下吗，我帮您查看一下。}
    ';

    const KEYWORDS = [
        '退回', '设备'
    ];

    const DESCRIPTION = '退回设备未收到';

    public function action(Stage $stageRoute): Navigator
    {
        return $stageRoute->build()
            ->askConfirm(
                '您的账号是%account%, 快递单号是%order%, 确认吗?'
            )->callback()
            ->hearing()
                ->isChoice(1, function(Dialog $dialog) {
                    return $dialog->goStage('final');
                })
                ->isChoice(0, function(Dialog $dialog) {
                    $dialog->say()->info('麻烦您再重新确认一下');
                    unset($this->account);
                    unset($this->order);
                    return $dialog->restart();
                })->end();

    }

    public function __onFinal(Stage $stage) : Navigator
    {
        return $stage->build()
            ->info('好的，已经查询到，目前这个快递还没有签收，还在派送中，这边我会帮您反馈一下这个问题，我们收到您的设备会跟您取得联系的')
            ->fulfill();
    }

    public function __exiting(Exiting $listener): void
    {
    }




}