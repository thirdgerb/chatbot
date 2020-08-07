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

use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Contracts\Trans\Translator;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Host\IIntentMsg;
use Commune\Message\Host\SystemInt\DialogConfuseInt;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ConfuseRenderer implements Renderer
{
    /**
     * @var Mindset
     */
    protected $mindset;

    /**
     * @var TranslatorRenderer
     */
    protected $translator;

    /**
     * ConfuseRenderer constructor.
     * @param Mindset $mindset
     * @param TranslatorRenderer $translator
     */
    public function __construct(Mindset $mindset, TranslatorRenderer $translator)
    {
        $this->mindset = $mindset;
        $this->translator = $translator;
    }


    public function __invoke(HostMsg $message): ? array
    {
        $confuseId = HostMsg\DefaultIntents::SYSTEM_DIALOG_CONFUSE;
        $intent = IIntentMsg::isIntent(
            $message,
            $confuseId
        );

        if (empty($intent)) {
            return null;
        }

        $awaitKey = DialogConfuseInt::ENTITY_AWAIT;
        $matchedKey = DialogConfuseInt::ENTITY_MATCHED;
        $entities = $intent->getEntities();
        $await = $entities[$awaitKey] ?? null;
        $matched = $entities[$matchedKey] ?? null;

        // 如果定义了 matched intent, 则使用 unable 回复.
        $transId = !empty($matched)
            ? HostMsg\DefaultIntents::SYSTEM_DIALOG_UNABLE
            : $confuseId;

        if (!empty($await)) {
            $awaitTransId = HostMsg\DefaultIntents::SYSTEM_DIALOG_UNABLE . "." . $await;

            $transId = $this->translator->isTranslatable($awaitTransId)
                ? $awaitTransId
                : $transId;
        }

        $matchedDesc = $this->parseMatched($matched);
        $awaitDesc = $this->parseAwait($await);
        $text = $this->translator->translate(
            $transId,
            [
                'await' => $awaitDesc,
                'matched' => $matchedDesc
            ]
        );

        return [
            IText::instance($text)
        ];
    }

    protected function parseAwait(? string $await) : string
    {
        if (empty($await)) {
            return '';
        }

        $stageReg = $this->mindset->stageReg();
        if ($stageReg->hasDef($await)) {
            return $stageReg->getDef($await)->getTitle();
        }

        return '';
    }

    protected function parseMatched(? string $matched) : string
    {
        if (empty($matched)) {
            return '';
        }

        $intentReg = $this->mindset->intentReg();
        if ($intentReg->hasDef($matched)) {
            return $intentReg->getDef($matched)->getTitle();
        }

        return '';
    }

}