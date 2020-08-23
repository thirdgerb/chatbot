<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Jieba;

use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Blueprint\NLU\Tokenizer;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\NLU\Support\ParserTrait;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\Intercom\InputMsg;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Commune\Blueprint\NLU\Tokenizer as ComprehendTokenizer;

/**
 * jieba 中文分词工具.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JiebaTokenizer implements Tokenizer
{
    use ParserTrait;

    protected static $booted = false;

    /**
     * @var NLUServiceOption
     */
    protected $option;

    /**
     * @var array
     */
    protected $map;

    /**
     * @var JiebaOption
     */
    protected $jiebaOption;

    public function __construct(
        NLUServiceOption $option,
        JiebaOption $jiebaOption
    )
    {
        $this->option = $option;
        $this->jiebaOption = $jiebaOption;
        self::boot($jiebaOption);
    }

    public static function isBooted() : bool
    {
        return self::$booted;
    }

    public static function boot(JiebaOption $option) : void
    {
        if (!self::$booted) {
            Jieba::init($option->toArray());
            Finalseg::init();
            self::$booted = true;
        }
    }


    public function getOption(): NLUServiceOption
    {
        return $this->option
            ?? $this->option = static::defaultOption();
    }

    public static function defaultOption()  : NLUServiceOption
    {
        return new NLUServiceOption([
            'id' => static::class,
            'desc' => '用 php-jieba 实现的 tokenizer',
            'serviceInterface' => Tokenizer::class,
            'serviceAbstract' => static::class,
            'listening' => [],
        ]);

    }


    public function saveMeta(Cloner $cloner, DefMeta $meta): string
    {
        // todo 未来可能加入同义词词典
        // 不过 php-jieba 本来就是测试用的, 不考虑生产环境再去复杂使用.
        return '';
    }

    public function syncMind(Mindset $mind): string
    {
        return '';
    }

    public function doParse(
        InputMsg $input,
        string $sentence,
        Session $session,
        Comprehension $comprehension
    ): Comprehension
    {
        $isSucceed = $comprehension->isSucceed(ComprehendTokenizer::class);
        if ($isSucceed) {
            return $comprehension;
        }

        $msg = $input->getMessage();
        if (!$msg instanceof VerbalMsg) {
            return $comprehension;
        }

        $tokens = $this->tokenize($sentence);
        $comprehension->tokens->addTokens($tokens);
        $comprehension->handled(
            ComprehendTokenizer::class,
            static::class,
            true
        );

        return $comprehension;
    }

    public function getDefaultStopWordMap() : array
    {
        if (isset($this->map)) {
            return $this->map;
        }

        $this->map = [];

        if (!file_exists($this->jiebaOption->stopWordsFile)) {
            return $this->map;
        }

        $content = file_get_contents($this->jiebaOption->stopWordsFile);
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            if (!empty($line)) {
                $this->map[$line] = true;
            }
        }

        return $this->map;
    }

    public function tokenize(string $sentence, array $stopWords = null): array
    {
        $sentence = preg_replace("/[^\p{L}\p{N}]+/u", ' ', $sentence);

        $words = Jieba::cut($sentence);

        if (is_null($stopWords)) {
            $map = $this->getDefaultStopWordMap();
            $results = [];
            foreach ($words as $word) {
                if (!array_key_exists($word, $map)) {
                    $results[] = $word;
                }
            }
            return $results;

        } elseif(!empty($stopWords)) {
            return array_diff($words, $stopWords);

        } else {
            return $words;
        }
    }


}