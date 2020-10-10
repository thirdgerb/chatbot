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

use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Host\IIntentMsg;
use Commune\Message\Host\SystemInt\DialogStageEventInt;
use Commune\Protocols\HostMsg;


/**
 * 根据 StageName + Intent 来渲染文本结果.
 * 可以默认用 Intent 指明的回复来返回结果
 * 也可以用 intent.stageName 的方式来指明结果.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageEventRenderer implements Renderer
{
    const PREFIX = 'stageEvent';

    /**
     * @var TranslatorRenderer
     */
    protected $translator;

    /**
     * StageEventRenderer constructor.
     * @param TranslatorRenderer $translator
     */
    public function __construct(TranslatorRenderer $translator)
    {
        $this->translator = $translator;
    }


    public function __invoke(HostMsg $message): ? array
    {
        $intent = IIntentMsg::isIntent($message, HostMsg\DefaultIntents::SYSTEM_DIALOG_STAGE_EVENT);

        if (empty($intent)) {
            return null;
        }

        $entities = $intent->getEntities();
        $stage = $entities[DialogStageEventInt::ENTITY_STAGE] ?? '';
        $event = $entities[DialogStageEventInt::ENTITY_EVENT] ?? '';
        $slots = $entities[DialogStageEventInt::ENTITY_SLOTS] ?? [];

        $transId = self::PREFIX . '.' . $event . '.' . $stage;

        if (!$this->translator->isTranslatable($transId)) {
            $transId = self::PREFIX . '.' . $event;
        }

        $text = $this->translator->translate(
            $transId,
            $slots
        );
        return [IText::instance($text, $message->getLevel())];
    }


}