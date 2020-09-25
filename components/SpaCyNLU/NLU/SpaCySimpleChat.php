<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\NLU;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindDef\ChatDef;
use Commune\Blueprint\Ghost\MindMeta\ChatMeta;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Blueprint\NLU\SimpleChat;
use Commune\Components\SpaCyNLU\Blueprint\SpaCyNLUClient;
use Commune\Components\SpaCyNLU\Configs\ChatModuleConfig;
use Commune\Components\SpaCyNLU\Managers\SimpleChatManager;
use Commune\Message\Host\Convo\IText;
use Commune\NLU\Support\ParserTrait;
use Commune\Protocals\Abstracted\Replies;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SpaCySimpleChat implements SimpleChat
{
    use ParserTrait;

    /**
     * @var SpaCyNLUClient
     */
    protected $client;

    /**
     * @var ChatModuleConfig
     */
    protected $config;

    /**
     * SpaCySimpleChat constructor.
     * @param SpaCyNLUClient $client
     * @param ChatModuleConfig $config
     */
    public function __construct(SpaCyNLUClient $client, ChatModuleConfig $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public static function defaultOption() : NLUServiceOption
    {
        return new NLUServiceOption([
            'id' => 'SpaCy simple chat',
            'desc' => 'SpaCy NLU 实现的简单闲聊组件',
            'serviceAbstract' => SpaCySimpleChat::class,
            'managerUcl' => SimpleChatManager::genUcl()->encode(),
            'priority' => NLUServiceOption::MIDDLE_PRIORITY,
            'strategy' => [
                'auth' => [Supervise::class],
            ],
        ]);
    }


    public function syncMind(Mindset $mind): ? string
    {
        $chatReg = $mind->chatReg();
        $gen = $chatReg->each();

        $chatData = [];
        foreach ($gen as $chatDef) {
            /**
             * @var ChatDef $chatDef
             */
            $index = $chatDef->getIndex();
            $data = [
                'cid' => $chatDef->getCid(),
                'say' => $chatDef->getSay(),
                'reply' => $chatDef->getReply()
            ];

            $chatData[$index][] = $data;
        }

        $json = json_encode($chatData, ArrayAndJsonAble::PRETTY_JSON);
        $path = $this->config->dataPath;

        file_put_contents($path, $json);
        return null;
    }

    public function doParse(
        InputMsg $input,
        string $text,
        Session $session,
        Comprehension $comprehension
    ): Comprehension
    {
        $reply = $this->reply($text);
        if (empty($reply)) {
            $comprehension->handled(
                Comprehension::TYPE_REPLIES,
                static::class,
                false
            );
            return $comprehension;
        }

        $comprehension->replies->addReplies( IText::instance($reply));
        $comprehension->handled(
            Comprehension::TYPE_REPLIES,
            static::class,
            true
        );
        return $comprehension;
    }


    public function saveMeta(Cloner $cloner, DefMeta $meta): ? string
    {
        if (! $meta instanceof ChatMeta) {
            return null;
        }

        $def = $meta->toWrapper();
        $error = $this->client->chatLearn($def);
        return $error;
    }

    public function reply(string $query, string $index = ''): ? string
    {
        $reply = $this->client->chatReply(
            $query,
            $this->config->threshold,
            $index
        );

        if (empty($reply)) {
            return null;
        }

        return $reply->reply;
    }

    protected function getHandlerType(): string
    {
        return Replies::class;
    }

}