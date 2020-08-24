<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Rasa;

use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\NLU\NLUService;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\NLU\Support\NLUUtils;
use Commune\NLU\Support\ParserTrait;
use Commune\Protocals\Abstracted\Intention;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\Intercom\InputMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RasaService implements NLUService
{
    use ParserTrait;

    /**
     * @var RasaComponent
     */
    protected $config;

    /**
     * @var ExceptionReporter
     */
    protected $reporter;

    public function getOption(): NLUServiceOption
    {
        return $this->config->nluOption;
    }

    public static function defaultOption() : NLUServiceOption
    {
        return new NLUServiceOption([
            'id' => 'rasa',
            'desc' => '基于 rasa 项目实现的 nlu service',
            'serviceInterface' => NLUService::class,
            'serviceAbstract' => static::class,
            'listening' => [],
        ]);
    }

    public function saveMeta(DefMeta $meta): ? string
    {
        // 不同步
        return null;
    }

    /**
     * 应该在异步的 service 里执行.
     * @param Mindset $mind
     * @return null|string
     */
    public function syncMind(Mindset $mind): ? string
    {
        $synchrony = new RasaSynchrony($this->config, $mind);
        $synchrony->outputCorpus();
        return null;
    }

    protected function doParse(
        InputMsg $input,
        string $text,
        Comprehension $comprehension
    ): Comprehension
    {
        // 不重复执行.
        if ($comprehension->isSucceed(Intention::class)) {
            return $comprehension;
        }

        try {

            $parsed = $this->request($text);

            $entities = $this->wrapEntities($parsed['entities'] ?? []);
            $ranking = $parsed['intent_ranking'] ?? [];

            if (empty($entities) && empty($ranking)) {
                return $comprehension;
            }

            if (!empty($entities)) {
                $comprehension->intention->setPublicEntities($entities);
            }

            $matchedIntent = $parsed['intent'] ?? null;
            if (!empty($matchedIntent)) {
                $possible = $this->addIntent($comprehension, $matchedIntent);

                if ($possible) {
                    $comprehension->intention->setMatchedIntent($matchedIntent['name']);
                }
            }

            foreach ($ranking as $item) {
                $this->addIntentToNLU($nlu, $item);
            }

            $comprehension->handled(Intention::class, static::class, true);


        } catch (\Throwable $e) {
            $comprehension->handled(Intention::class, static::class, false);
            $this->reporter->report($e);
        }

        return $comprehension;
    }


}