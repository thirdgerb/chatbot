<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Strategies;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Components\HeedFallback\Constants\HeedFallbackLang;
use Commune\Components\HeedFallback\Libs\FallbackStrategy;
use Commune\Contracts\Trans\Translator;
use Commune\Ghost\Context\ACodeContext;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id;
 */
class TransStrategy extends ACodeContext implements FallbackStrategy
{
    const PREFIX = 'fallbackStrategy.trans.';

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 0,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => ['id'],

        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog, Translator $translator) {

                $id = static::PREFIX . $this->id;
                if ($translator->hasTemplate($id)) {
                    $lang = $translator->trans($id);
                    return $dialog
                        ->send()
                        ->info($lang)
                        ->over()
                        ->fulfill();
                }

                if ($dialog->cloner->auth->allow(Supervise::class)) {
                    return $dialog->goStage('create');
                }

                return $dialog
                    ->send()
                    ->notice(HeedFallbackLang::REPLY_IS_PREPARING)
                    ->over()
                    ->rewind();
            });
    }

    public function __on_create(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->askVerbal(
                        HeedFallbackLang::REQUIRE_TRANS_REPLY
                    );

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function (
                        Dialog $dialog,
                        AnswerMsg $isAnswered,
                        Translator $translator
                    ) {
                        $answer = $isAnswered->getAnswer();
                        if (empty($answer)) {
                            return $dialog
                                ->send()
                                ->error('reply is empty!')
                                ->over()->rewind();
                        }

                        $id = static::PREFIX . $this->id;

                        $translator->saveMessages([$id => $answer]);

                        return $dialog->send()
                            ->info('success!')
                            ->over()
                            ->goStage('start');
                    })
                    ->end();
            });

    }

    public static function onCreation(string $id): Ucl
    {
        return static::genUcl(['id' => $id]);
    }

    public static function onHandler(string $id): Ucl
    {
        return static::genUcl(['id' => $id]);
    }


}