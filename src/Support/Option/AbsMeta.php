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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $wrapper   目标 Option 的类名. 允许用别名.
 * @property-read array $config     wrapper 对应的配置.
 */
abstract class AbsMeta extends AbsOption implements Meta
{

    abstract public static function validateWrapper(string $wrapper) : ? string;

    public function getWrapper(): Wrapper
    {
        $wrapperName = $this->wrapper;
        return call_user_func([$wrapperName, Wrapper::INIT_FUNC], $this);
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $wrapper = $data['wrapper'] ?? '';
        return static::validateWrapper($wrapper) ?? parent::validate($data);
    }

}