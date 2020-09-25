<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Ghost\Support\CommandUtils;
use Commune\Protocals\Abstracted;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property null|AnswerMsg $answer
 * @property null|string $cmd
 * @property array $emotions
 * @property array $intention
 * @property null|HostMsg[] $replies
 * @property null|array $tokens
 * @property string|null $redirection
 * @property array $handledBy
 */
class IComprehension extends AbsMessage implements
    Comprehension,
    Abstracted\Emotion,
    Abstracted\Replies,
    Abstracted\Tokenize,
    Abstracted\Cmd,
    Abstracted\Answer,
    Abstracted\Routing
{
    protected $transferNoEmptyRelations = true;

    protected $transferNoEmptyData = true;

    public static function stub(): array
    {
        return [
            'answer' => null,
            'cmd' => null,
            'emotions' => [],
            'intention' => [],
            'replies' => null,
            'tokens' => null,
            'redirection' => null,
            'handledBy' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'intention' => IIntention::class,
        ];
    }


    /*------- command -------*/

    public function setCmdStr(string $command): void
    {
        $this->_data['cmd'] = $command;
    }

    public function hasCmdStr(): bool
    {
        return isset($this->_data['cmd']);
    }

    public function getCmdStr(): ? string
    {
        return $this->_data['cmd'] ?? null;
    }

    public function getCmdName(): ? string
    {
        $str = $this->getCmdStr();

        return isset($str)
            ? CommandUtils::getCommandNameStr($str)
            : null;
    }


    /*------- emotions -------*/

    public function setEmotion(string $emotion, bool $bool): void
    {
        $this->_data['emotions'][$emotion] = $bool;
    }

    public function getEmotions(): array
    {
        $emotions = $this->_data['emotions'] ?? [];
        return array_keys($emotions);
    }

    public function addEmotions(string $emotion, string ...$emotions): void
    {
        array_unshift($emotions, $emotion);
        foreach ($emotions as $emotion) {
            $this->setEmotion($emotion, true);
        }
    }


    public function isEmotion(string $emotionName): ? bool
    {
        return $this->_data['emotions'][$emotionName] ?? null;
    }

    /*------- routing -------*/
    public function setRedirection(string $routeName): void
    {
        $this->_data['redirection'] = $routeName;
    }

    public function getRedirection(): ? string
    {
        return $this->_data['redirection'] ?? null;
    }


    /*------- replies -------*/

    public function addReplies(HostMsg ...$message): void
    {
        $this->_data['replies'] = $message;
    }

    public function hasReplies(): bool
    {
        return isset($this->_data['replies']);
    }

    public function getReplies(): ? array
    {
        return $this->_data['replies'] ?? null;
    }


    /*------- tokens -------*/

    public function addTokens(array $tokens): void
    {
        $this->_data['tokens'] = $tokens;
    }

    public function getTokens(): ? array
    {
        return $this->_data['tokens'] ?? null;
    }

    public function hasTokens(): bool
    {
        return isset($this->_data['tokens']);
    }


    /*------- handled by -------*/

    public function handled(
        string $type,
        string $comprehenderId,
        bool $success
    ): void
    {
        $handledBy = $this->_data['handledBy'];
        $handledBy[$type][$comprehenderId] = $success;
        $this->_data['handledBy'] = $handledBy;
    }

    public function isHandled(string $type): bool
    {
        return isset($this->_data['handledBy'][$type]);
    }


    public function isHandedBy(
        string $comprehenderId,
        string $type = null
    ): bool
    {

        if (isset($type)) {
            return isset($this->_data['handledBy'][$type][$comprehenderId]);
        }

        $handledBy = $this->_data['handledBy'];
        foreach ($handledBy as $type => $results) {
            if (isset($results[$comprehenderId])) {
                return true;
            }
        }
        return false;
    }

    public function isSucceed(
        string $type,
        string $comprehenderId = null
    ): bool
    {
        $results = $this->_data['handledBy'][$type] ?? [];

        if (isset($comprehenderId)) {
            return $results[$comprehenderId];
        }

        foreach ($results as $val) {
            if ($val) {
                return true;
            }
        }
        return false;
    }

    /*------- answer -------*/

    public function setAnswer(AnswerMsg $answer): void
    {
        $this->_data['answer'] = $answer;
    }

    public function getAnswer(): ? AnswerMsg
    {
        return $this->_data['answer'] ?? null;
    }


    /*------- methods -------*/

    public function isEmpty(): bool
    {
        $obj = $this->_data;
        foreach ($obj as $key => $value) {
            if ($value instanceof AbsMessage && !$value->isEmpty()) {
                return false;
            }

            if (!empty($value)) {
                return false;
            }
        }

        return true;
    }

    public function __get($name)
    {
        if ($name === 'intention') {
            return parent::__get($name);
        }
        return $this;
    }

    public function getText(): string
    {
        return $this->toPrettyJson();
    }

}
