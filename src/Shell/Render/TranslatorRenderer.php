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

use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Contracts\Trans\SelfTranslatable;
use Commune\Contracts\Trans\Translatable;
use Commune\Contracts\Trans\Translator;
use Commune\Message\Host\Convo\IText;
use Commune\Protocals\HostMsg;


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
     * TranslatorRender constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
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
            $id = $message->getProtocalId();
            $text = $this->translator->trans($id, $slots);
            return [IText::instance($text, $message->getLevel())];
        }

        // 无法翻译的不渲染.
        return null;
    }


}