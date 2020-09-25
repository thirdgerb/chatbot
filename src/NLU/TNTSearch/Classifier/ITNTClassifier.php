<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\TNTSearch\Classifier;

use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Blueprint\NLU\SentenceClassifier;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Ghost\IMindDef\Intent\IIntentExample;
use Commune\NLU\Support\ParserTrait;
use Commune\Protocals\Abstracted\Intention;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Utils\StringUtils;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;
use TeamTNT\TNTSearch\Support\TokenizerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ITNTClassifier implements SentenceClassifier
{
    use ParserTrait;

    /**
     * @var TNTClassifier
     */
    protected $classifier;

    /**
     * @var NLUServiceOption
     */
    protected $option;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var float $threshold;
     */
    protected $threshold;

    public function __construct(
        NLUServiceOption $option,
        TokenizerInterface $tokenizer,
        string $filePath,
        float $threshold
    )
    {
        $this->option = $option;
        $this->filePath = $filePath;
        $this->threshold = $threshold;

        $this->classifier = new TNTClassifier();
        $this->classifier->tokenizer = $tokenizer;

        if (file_exists($this->filePath)) {
            $this->classifier->load($this->filePath);
        }
    }


    public function getOption(): NLUServiceOption
    {
        return $this->option;
    }

    public function saveAll() :void
    {
        $this->classifier->save($this->filePath);
    }

    public function saveMeta(DefMeta $meta): ? string
    {
        if (!$meta instanceof IntentMeta) {
            return null;
        }

        $examples = $meta->examples;
        if (empty($examples)) {
            return null;
        }

        $name = $meta->name;
        foreach ($examples as $example) {
            $intentExample = new IIntentExample($example);
            $this->learn($intentExample->getText(), $name);
        }

        return null;
    }

    public function syncMind(Mindset $mind): ? string
    {
        $reg = $mind->intentReg();
        $i = 0;
        foreach ($reg->each() as $def) {
            $i ++ ;
            $this->saveMeta($def->toMeta());
        }
        return null;
    }

    public function doParse(
        InputMsg $input,
        string $sentence,
        Session $session,
        Comprehension $comprehension
    ): Comprehension
    {
        if (
            StringUtils::isEmptyStr($sentence)
            || is_numeric($sentence)
            // 单字符.
            || preg_match('/^\w$/', $sentence)
        ) {
            return $comprehension;
        }

        $type = $this->predict($sentence);

        if (isset($type)) {
            $comprehension->intention->setMatchedIntent($type);
            $comprehension->handled(
                Comprehension::TYPE_INTENTION,
                static::class,
                true
            );
        } else {
            $comprehension->handled(
                Comprehension::TYPE_INTENTION,
                static::class,
                false
            );
        }

        return $comprehension;
    }

    public function learn(string $text, string $type)
    {
        $this->classifier->learn($text, $type);
    }

    public function predict(string $text): ? string
    {
        $guess = $this->classifier->predict($text);
        return $guess['label'] ?? null;
    }


}