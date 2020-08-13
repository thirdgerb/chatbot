<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Host\SystemInt\DialogRequireInt;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $query
 * @property-read string|null $validator
 */
class AttrStageDef extends AStageDef
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'contextName' => '',
            'title' => '',
            'desc' => '',
            'query' => '',
            'validator' => null,
            'stageName' => '',
            'asIntent' => [],
            'events' => [],
            'ifRedirect' => null,
        ];
    }


    public function onActivate(Activate $dialog): Operator
    {
        $name = $this->getStageShortName();
        $value = $dialog->context[$name];

        if (isset($value)) {
            return $dialog->next();
        }

        $query = $this->query;
        $queryMsg = empty($query)
            ? DialogRequireInt::instance($name)
            : IText::instance($query);

        // 要求一个纯文字的输入.
        return $dialog
            ->send()
            ->message($queryMsg)
            ->over()
            ->await();
    }

    public function onReceive(Receive $dialog): Operator
    {
        $validator = $this->validator;

        if (isset($validator)) {
            return $dialog->container()->action($validator);
        }

        $name = $this->stageName;

        // 获得输入后, 进入下一步.
        return $dialog
            ->hearing()
            ->isVerbal()
            ->then(function (VerbalMsg $isVerbal, Dialog $dialog) use ($name) {

                $dialog->context[$name] = $isVerbal->getText();
                return $dialog->next();
            })
            ->end();
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return null;
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $dialog->reactivate();
    }


}