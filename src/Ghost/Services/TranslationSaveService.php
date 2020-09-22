<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Services;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Contracts\Trans\Translator;
use Commune\Ghost\Services\Payload\TransSavePayload;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TranslationSaveService extends AbsDialogicService
{

    /**
     * @var Translator
     */
    protected $translator;


    protected $auth = [
        Supervise::class,
    ];

    /**
     * TranslationSaveService constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * @param array $payload
     * @see TransSavePayload
     *
     * @param Deliver $deliver
     */
    public function handle(array $payload, Deliver $deliver): void
    {
        $option = new TransSavePayload($payload);
        $this->translator->saveMessages(
            $option->messages,
            $option->locale,
            $option->domain,
            $option->intl ?? true,
            $option->force ?? true
        );

        $count = count($option->messages);
        $deliver->info("saved $count messages to translator");
    }


}