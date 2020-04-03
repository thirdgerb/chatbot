<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Framework\Contracts\Cache;
use Commune\Message\Blueprint\QuestionMsg;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Session\ShlSessionStorage;
use Commune\Shell\ShellConfig;
use Commune\Support\Babel\Babel;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShlSessionStorage implements ShlSessionStorage, HasIdGenerator
{
    use IdGeneratorHelper;

    const SESSION_DATA_KEY = 'shell:%s:session:%s';
    const QUESTION_KEY = 'shellQuestion';

    /**
     * @var string
     */
    protected $shellChatId;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var ShellConfig
     */
    /*--- cached ---*/

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int|null
     */
    protected $expire;

    /**
     * @var bool
     */
    protected $changed = false;

    /**
     * @var string
     */
    protected $cacheKey;


    /**
     * @var bool
     */
    protected $init = false;

    /**
     * IShlSessionStorage constructor.
     * @param ShlSession $session
     * @param ShellConfig $config
     */
    public function __construct(ShlSession $session, ShellConfig $config)
    {
        $this->shellChatId = $session->getChatId();
        $this->cache = $session->cache;
        $expire = $config->sessionExpire;
        $this->expire = $expire > 0 ? $expire : null;

        $this->cacheKey = printf(
            static::SESSION_DATA_KEY,
            $config->shellName,
            $this->shellChatId
        );

        $dataVal = $this->cache->get($this->cacheKey);
        if (!empty($dataVal)) {
            $decoded = json_decode($dataVal, true);
            if (is_array($decoded)) {
                $this->setAll($decoded);
            }
        }
    }

    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
        $this->changed = true;
    }

    public function setAll(array $values): void
    {
        $this->data = $values;
        $this->changed = true;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function setQuestion(QuestionMsg $question): void
    {
        $this->set(static::QUESTION_KEY, Babel::getResolver()->serialize($question));
    }

    public function getQuestion(): ? QuestionMsg
    {
        $value = $this->get(static::QUESTION_KEY);

        return isset($value)
            ? Babel::getResolver()->unSerialize($value)
            : null;
    }

    public function save(): void
    {
        // 数据有修改过
        if ($this->changed) {
            $this->cache->set($this->cacheKey, json_encode($this->data), $this->expire);

        // 如果 Session 有过期时间
        } elseif ($this->expire > 0 ) {
            $this->cache->expire($this->cacheKey, $this->expire);
        }


    }


}