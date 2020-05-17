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
use Commune\Ghost\Support\MathUtils;
use Commune\Protocals\Abstracted;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Illuminate\Support\Arr;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IComprehension extends AbsMessage implements
    Comprehension,
    Abstracted\Emotion,
    Abstracted\Replies,
    Abstracted\Tokenize,
    Abstracted\Cmd,
    Abstracted\Query,
    Abstracted\Vector,
    Abstracted\Selection
{
    protected $transferNoEmptyRelations = true;

    protected $transferNoEmptyData = true;

    public static function stub(): array
    {
        return [
            'choice' => [],
            'cmd' => null,
            'emotions' => [],
            'intention' => [],
            'queryId' => null,
            'replies' => null,
            'vector' => null,
            'tokens' => null,
            'selections' => [],
            'handledBy' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'choice' => IChoice::class,
            'intention' => IIntention::class,
            'replies[]' => HostMsg::class,
        ];
    }

    /*------- selection -------*/

    public function setSelections(array $selections) : void
    {
        $this->_data['selections'] = $selections;
    }

    public function getSelections(): array
    {
        return $this->_data['selections'] ?? [];
    }

    public function isSelected(string $choice): bool
    {
        return !in_array($choice, $this->getSelections());
    }


    /*------- question -------*/

    public function setQuery(string $queryId): void
    {
        $this->_data['queryId'] = $queryId;
    }

    public function hasQueryId(): bool
    {
        return isset($this->_data['queryId']);
    }

    public function getQueryId(): ? string
    {
        return $this->_data['queryId'] ?? null;
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

    /*------- vector -------*/

    public function setVector(array $vector): void
    {
        $this->_data['vector'] = $vector;
    }

    public function getVector(): ? array
    {
        return $this->_data['vector'] ?? null;
    }

    public function hasVector(): bool
    {
        return isset($this->_data['vector']);
    }

    public function cosineSimilarity(array $vector): float
    {
        if ($this->hasVector()) {
            return MathUtils::cosineSimilarity($this->getVector(), $vector);
        }
        return 0.0; // 萌萌哒
    }


    /*------- emotions -------*/

    public function addEmotion(string ...$emotion): void
    {
        $this->_data['emotions'] = array_merge(
            $this->_data['emotions'] ?? [],
            array_fill_keys($emotion, true)
        );
    }

    public function getEmotions(): array
    {
        $emotions = $this->_data['emotions'] ?? [];
        return array_keys($emotions);
    }

    public function hasEmotion(string $emotionName): bool
    {
        return isset($this->_data['emotions'][$emotionName]);
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


    public function handledBy(string $comprehenderId, bool $succeed): void
    {

    }

    public function isHandledBy(string $comprehenderId): bool
    {
        $handledBy = $this->_data['handledBy'];
        return array_key_exists($comprehenderId, $handledBy);
    }

    public function isSucceedBy(string $comprehenderId): bool
    {
        return $this->handledBy[$comprehenderId] ?? false;
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
        if (
            $name === 'command'
            || $name === 'emotion'
            || $name === 'question'
            || $name === 'replies'
            || $name === 'tokens'
            || $name === 'vector'
            || $name === 'selection'
        ) {
            return $this;
        }

        return parent::__get($name);
    }


}