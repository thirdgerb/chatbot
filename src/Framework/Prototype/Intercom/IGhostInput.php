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
use Commune\Framework\Blueprint\Intercom\GhostOutput;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Prototype\Abstracted\IComprehension;
use Commune\Message\Blueprint\IntentMsg;
use Commune\Message\Blueprint\Message;
use Commune\Message\Blueprint\Tag\Verbal;
use Commune\Message\Prototype\IIntentMsg;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IGhostInput extends AGhostMsg implements GhostInput
{
    const PROPERTIES = [
        'shellName' => 'shn',
        'chatId' => 'cid',
        'stateless' => 'stt',
        'shellMessage' => 'shm',
        'traceId' => 'tid',
        'sceneId' => 'sid',
        'sceneEnv' => 'env',
        'messageId' => 'mid',
        'comprehension' => 'cph',
    ];

    /**
     * @var bool
     */
    protected $stt;

    /**
     * @var string
     */
    protected $sid;

    /**
     * @var array
     */
    protected $env;

    /**
     * @var Comprehension
     */
    protected $cph;

    /**
     * @var ShellInput
     */
    protected $shm;

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
        bool $stateless,
        ShellInput $shellMessage,
        string $traceId,
        string $sceneId,
        array $sceneEnv,
        string $messageId = null,
        Comprehension $comprehension = null
    )
    {
        $this->stt = $stateless;
        $this->sid = $sceneId;
        $this->env = $sceneEnv;
        $this->cph = $comprehension ?? new IComprehension();
        parent::__construct($shellName, $chatId, $shellMessage, $traceId, $messageId);
    }

    public static function createNewSerializable(array $input): ? BabelSerializable
    {
        return new static(
            $input['shn'],
            $input['cid'],
            $input['stt'],
            $input['shm'],
            $input['tid'],
            $input['cid'],
            $input['env'],
            $input['mid'],
            $input['cph']
        );
    }

    /*-------- methods ---------*/

    public function getTrimmedText(): string
    {
        if (isset($this->trimmed)) {
            return $this->trimmed;
        }

        $recognition = $this->cph->recognition->getRecognition();
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

        $intentRepo = $this->cph->intent;
        $matchedIntentName = $intentRepo->getMatchedIntent();
        if (isset($matchedIntentName)) {
            return $this->matchedIntent = new IIntentMsg(
                $matchedIntentName,
                $intentRepo->getIntentEntities($matchedIntentName)
            );
        }

        return null;
    }

    public function reply(Message $message, int $deliverAt = null): GhostOutput
    {
        return new IGhostOutput(
            $this->shellName,
            $this->chatId,
            $this->shellMessage->output($message),
            $deliverAt
        );
    }


    public function __sleep(): array
    {
        $fields = parent::__sleep();
        return array_merge($fields, ['sid', 'stt', 'env', 'cph']);
    }


}