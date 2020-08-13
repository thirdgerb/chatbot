<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Option;

use Commune\Blueprint\Exceptions\CommuneLogicException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $wrapper   目标 Option 的类名. 允许用别名.
 * @property-read array $config     wrapper 对应的配置.
 */
abstract class AbsMeta extends AbsOption implements Meta
{

    public function toWrapper(): Wrapper
    {
        $wrapperName = $this->wrapper;
        if (!is_a($wrapperName, Wrapper::class, true)) {

            $json = $this->toJson();
            $current = static::class;
            $expect = Wrapper::class;
            throw new CommuneLogicException(
                "$current toWrapper expect $expect, \"$wrapperName\" given. data: $json"
            );
        }
        return call_user_func([$wrapperName, Wrapper::WRAP_META_FUNC], $this);
    }

}