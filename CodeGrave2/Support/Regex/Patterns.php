<?php


namespace Commune\Support\Regex;


class Patterns
{
    // 中文
    const chinese_char = '[\x{4e00}-\x{9fa5}]';

    // 英文字符
    const english_char = '[a-zA-Z]';

    // 中文 + 英文 + 数字
    const CH_EN_NUM_CHAR = '[\x{4e00}-\x{9fa5}a-zA-Z0-9]';

    const single_digit_char = '[x00-xff]';

    const multi_digit_char = '[^x00-xff]';

    // 中国手机号匹配
    const ch_phone_pattern = '/^((13[0-9])|(14[0-9])|(15[0-9])|(17[0-9])|(18[0-9]))\d{8}$/';

}