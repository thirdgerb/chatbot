<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Render;

use Commune\Blueprint\Shell;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Contracts\Trans\SelfTranslatable;
use Commune\Contracts\Trans\Translatable;
use Commune\Contracts\Trans\Translator;
use Commune\Message\Host\Convo\IText;
use Commune\Protocols\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TranslatorRenderer implements Renderer
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string|null
     */
    protected $lang = null;

    /**
     * @var array
     */
    protected $environmentSlots = [];

    /**
     * TranslatorRenderer constructor.
     * @param Translator $translator
     * @param Shell\ShellSession $session
     * @param array $environmentSlots
     */
    public function __construct(
        Translator $translator,
        Shell\ShellSession $session,
        array $environmentSlots = []
    )
    {
        $this->translator = $translator;
        $this->domain = $session->getAppId();
        $this->environmentSlots = $environmentSlots;
        // todo 未来考虑从 session 中获取 lang 的参数.
    }

    public function __invoke(HostMsg $message): ? array
    {
        // 消息标注为可以自翻译.
        if ($message instanceof SelfTranslatable) {
            $message->translate($this->translator);
            return [$message];
        }

        // 可以翻译的协议有 temp 和 slots
        if ($message instanceof Translatable) {
            $slots = $message->getSlots();
            $id = $message->getProtocolId();
            $text = $this->translate($id, $slots);
            return [IText::instance($text, $message->getLevel())];
        }

        // verbal msg 默认不翻译. 想要翻译的类型请加上 translatable
        if ($message instanceof HostMsg\Convo\VerbalMsg) {
            return [$message];
        }

        // 无法翻译的不渲染.
        return null;
    }

    public function isTranslatable(string $id)  :bool
    {
        $hasShellTransId = $this->translator->hasTemplate(
                $id,
                $this->domain,
                $this->lang
        );

        return $hasShellTransId || $this->translator->hasTemplate($id);
    }

    public function translate(
        string $id,
        array $slots
    ) : string
    {
        $hasTemp = $this->translator->hasTemplate(
            $id,
            $this->domain,
            $this->lang
        );

        $slots = $slots + $this->environmentSlots;

        if ($hasTemp) {
            return $this
                ->translator
                ->trans($id, $slots, $this->domain, $this->lang);
        }

        return $this->translator
            ->trans($id, $slots);
    }

}