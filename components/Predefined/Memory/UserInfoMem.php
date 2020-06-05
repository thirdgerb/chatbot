<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Memory;

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Ghost\Context\AMemoryContext;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read int $loginTimes
 *
 * # 用注解的方式定义 Context 的标题和简介.
 *
 * @title 用户信息
 * @desc 了解用户信息
 */
class UserInfoMem extends AMemoryContext
{
    public static function __scopes(): array
    {
        return [ClonerScope::GUEST_ID];
    }

    public static function __defaults(): array
    {
        return ['loginTimes' => 0];
    }


    public static function __depending(Depending $depending): Depending
    {
        return $depending->on('name');
    }

    public function increaseLoginTimes() : void
    {
        $this->loginTimes = $this->loginTimes + 1;
    }

    /**
     * @param Stage $stage
     * @return Stage
     *
     * @desc 请问应该如何称呼您
     */
    public function __on_name(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog){
                return $dialog
                    ->await()
                    ->askVerbal('请问应该如何称呼您');
            })
            ->onReceive(function(Dialog $dialog) {

                    return $dialog
                        ->hearing()
                        ->isAnswered()
                        ->then(function(Dialog $dialog, AnswerMsg $isAnswered){
                            $answer = $isAnswered->getAnswer();
                            if (mb_strlen($answer) > 10) {
                                $dialog
                                    ->send()
                                    ->notice("请把称呼控制在10个字符之内");

                                return $dialog->rewind();
                            }

                            $this->name = $answer;
                            return $dialog->next();
                        })
                        ->end();

            });
    }


}