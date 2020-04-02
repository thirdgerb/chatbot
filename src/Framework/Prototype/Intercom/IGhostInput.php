<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Intercom;

use Commune\Framework\Blueprint\Abstracted\Comprehension;
use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellMsg;
use Commune\Framework\Prototype\Abstracted\IComprehension;
use Commune\Message\Blueprint\IntentMsg;
use Commune\Message\Blueprint\Tag\Verbal;
use Commune\Message\Prototype\IIntent;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IGhostInput extends AGhostMsg implements GhostInput
{

    /**
     * @var string
     */
    protected $sceneId;

    /**
     * @var array
     */
    protected $sceneEnv;

    /**
     * @var Comprehension
     */
    protected $comprehension;


    /*------ cached -------*/

    /**
     * @var string
     */
    protected $trimmed;

    /**
     * @var IntentMsg|null
     */
    protected $matchedIntent;


    /*-------- construct ---------*/

    public function __construct(
        string $shellName,
        string $chatId,
        ShellInput $shellMessage,
        string $sceneId,
        array $sceneEnv,
        string $messageId = null,
        Comprehension $comprehension = null
    )
    {
        $this->sceneId = $sceneId;
        $this->sceneEnv = $sceneEnv;
        $this->comprehension = $comprehension ?? new IComprehension();
        parent::__construct($shellName, $chatId, $shellMessage, $messageId);
    }


    /*-------- methods ---------*/

    public function getTrimmedText(): string
    {
        if (isset($this->trimmed)) {
            return $this->trimmed;
        }

        $recognition = $this->comprehension->recognition->getRecognition();
        if (isset($recognition)) {
            return $this->trimmed = StringUtils::trim($recognition);
        }

        $message = $this->shellMessage->message;
        if ($message instanceof Verbal) {
            return $this->trimmed = $message->getTrimmedText();
        }

        return $this->trimmed = '';
    }

    public function getMatchedIntent(): ? IntentMsg
    {
        if (isset($this->matchedIntent)) {
            return $this->matchedIntent;
        }

        $message = $this->shellMessage->message;
        if ($message instanceof IntentMsg) {
            return $this->matchedIntent = $message;
        }

        $intentRepo = $this->comprehension->intent;
        $matchedIntentName = $intentRepo->getMatchedIntent();
        if (isset($matchedIntentName)) {
            return $this->matchedIntent = new IIntent(
                $matchedIntentName,
                $intentRepo->getIntentEntities($matchedIntentName)
            );
        }

        return null;
    }

    public function __sleep(): array
    {
        $fields = parent::__sleep();
        return array_merge($fields, ['sceneId', 'sceneEnv', 'comprehension']);
    }


}