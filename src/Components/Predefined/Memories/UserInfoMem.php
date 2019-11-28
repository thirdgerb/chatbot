<?php

namespace Commune\Components\Predefined\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property string $name ask.name
 * @property int $loginTimes
 */
class UserInfoMem extends MemoryDef
{
    const DESCRIPTION = '用户基本信息';

    const SCOPE_TYPES = [Scope::USER_ID];

    protected function init(): array
    {
        return [
            'loginTimes' => 0,
        ];
    }

    public function increaseLoginTimes() : void
    {
        $this->loginTimes = $this->loginTimes + 1;
    }

    public function __onName(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal('我应该如何称呼您')
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Answer $answer){
                $result = $answer->toResult();
                if (mb_strlen($result) > 10) {
                    $dialog->say()->warning("称呼请麻烦控制在10个字符之内");
                    return $dialog->repeat();
                }
                $this->name = $result;
                return $dialog->next();
            })
            ->end();
    }

}