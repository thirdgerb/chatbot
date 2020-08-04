<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Trans;


/**
 * 纯文本的渲染. 理论上要允许多语言, 以及 intl
 *
 * 原来的计划是用 symfony.
 * 但由于现在要做的是从配置里即时读写, 所以用 OptRegistry 来实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Translator
{
    const DEFAULT_LOCALE = 'zh-cn';
    const DEFAULT_DOMAIN = 'messages';

    public function trans(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $lang = null
    ) : string;

    /**
     * @param array $messages
     * @param string|null $locale
     * @param string|null $domain
     * @param bool $intl            默认使用 {} mustache
     * @param bool|null $force
     */
    public function saveMessages(
        array $messages,
        string $locale = null,
        string $domain = null,
        bool $intl = true,
        bool $force = null
    ) : void;

    public function getDefaultLocale() : string;

}