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
use Commune\Blueprint\Ghost\MindDef\Intent\IntentExample;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\NLU\NLUService;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Components\SpaCyNLU\Blueprint\SpaCyNLUClient;
use Commune\Components\SpaCyNLU\Configs\NLUModuleConfig;
use Commune\Components\SpaCyNLU\Managers\NLUServiceManager;
use Commune\NLU\Support\ParserTrait;
use Commune\Protocals\Abstracted\Intention;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * 使用 spacy nlu 项目实现的简单 nlu 中间件.
 *
 * @see https://github.com/thirdgerb/spacy-nlu
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SpaCyNLUService implements NLUService
{
    use ParserTrait;

    /**
     * @var SpaCyNLUClient
     */
    protected $client;

    /**
     * @var NLUModuleConfig
     */
    protected $config;

    /**
     * IntentMatcher constructor.
     * @param SpaCyNLUClient $client
     * @param NLUModuleConfig $config
     */
    public function __construct(
        SpaCyNLUClient $client,
        NLUModuleConfig $config
    )
    {
        $this->client = $client;
        $this->config = $config;
    }


    public static function defaultOption() : NLUServiceOption
    {
        return new NLUServiceOption([
            'id' => 'SpaCy intent matcher',
            'desc' => '使用 SpaCy nlu 实现的意图识别组件',
            'serviceAbstract' => SpaCyNLUService::class,
            'managerUcl' => NLUServiceManager::genUcl()->encode(),
            'priority' => NLUServiceOption::MIDDLE_PRIORITY + 2,
            'strategy' => [
                'auth' => [Supervise::class],
            ],
        ]);
    }

    public function syncMind(Mindset $mind): ? string
    {

        $intentMap = [];
        $intentReg = $mind->intentReg();
        $gen = $intentReg->each();
        foreach ($gen as $intentDef) {
            /**
             * @var IntentDef $intentDef
             */
            $intentName = $intentDef->getIntentName();

            $examples = array_map(function(IntentExample $example) {
                return $example->getText();
            }, $intentDef->getExampleObjects());

            if (empty($examples)) {
                continue;
            }

            $intentMap[$intentName] = $examples;
        }

        $path = $this->config->dataPath;
        $json = json_encode($intentMap, ArrayAndJsonAble::PRETTY_JSON);
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
        if ($session instanceof Cloner) {
            $routes = $session->runtime->getCurrentAwaitRoutes();
            if (empty($routes)) {
                $possibles = [];
            } else {
                $routes = array_filter($routes, function(Ucl $ucl) {
                    return $ucl->isValidPattern();
                });
                $possibles = array_map(function (Ucl $ucl) {
                    return $ucl->getIntentName();
                }, $routes);
            }

        } else {
            $possibles = [];
        }

        $predictions = $this->client->intentPredict(
            $text,
            $possibles,
            $this->config->threshold,
            $this->config->matchLimit
        );

        if (empty($predictions)) {
            $comprehension->handled(
                Intention::class,
                static::class,
                false
            );
            return $comprehension;
        }

        $intention = $comprehension->intention;
        foreach ($predictions as $prediction) {
            $intention->addPossibleIntent(
                $prediction->getIntentName(),
                ceil($prediction->similarity * 100),
                true
            );
        }

        $comprehension->handled(
            Intention::class,
            static::class,
            true
        );
        return $comprehension;
    }

    public function saveMeta(Cloner $cloner, DefMeta $meta): ? string
    {
        if (!$meta instanceof IntentMeta) {
            return null;
        }

        $def = $meta->toWrapper();
        return $this->client->intentLearn($def);
    }


}