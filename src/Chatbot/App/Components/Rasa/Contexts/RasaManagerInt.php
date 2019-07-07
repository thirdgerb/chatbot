<?php


namespace Commune\Chatbot\App\Components\Rasa\Contexts;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Components\Rasa\RasaNLUPipe;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RasaManagerInt extends AbsCmdIntent
{
    const DESCRIPTION = 'rasa意图管理';

    const CONTEXT_TAGS = [
        Definition::TAG_MANAGER
    ];

    const EXAMPLES = [
        '测试意图命中',
        '测试命中的意图',
        '测试命中了什么意图',
        '测试 matched intent',
        '查看命中的意图',
    ];

    public static function getMatcherOption(): IntentMatcherOption
    {
        return new IntentMatcherOption([
            'signature' => 'matchedIntent'
        ]);
    }

    public static function getContextName(): string
    {
        return 'rasa.manager';
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info(
                "进入rasa意图管理. 
请输入语句, 会给出命中的意图. 
输入'b'退出语境
输入'w'会保存rasa的model配置"
            )
            ->wait()
            ->hearing()
            ->is('b', function(Dialog $dialog){
                return $dialog->fulfill();
            })
            ->is('w', function(Dialog $dialog) {

                $isSupervisor = $dialog->session
                    ->conversation
                    ->isAbleTo(Supervise::class);

                if ($isSupervisor) {
                    /**
                     * @var RasaNLUPipe $pipe
                     */
                    $pipe = $dialog->app->make(RasaNLUPipe::class);
                    $pipe->outputIntentExamples(IntentRegistrar::getIns());
                    $dialog->say()->info('保存完毕');
                } else {
                    $dialog->say()->error('没有权限');
                }

                return $dialog->repeat();
            })
            ->end(function(Dialog $dialog){

                $matched = $dialog->session->getMatchedIntent();

                if (isset($matched)) {
                    $dialog->say()
                        ->info("命中的意图: \n" . $matched->toPrettyJson());

                }

                $incomingMessage = $dialog->session->incomingMessage;
                $collection = $incomingMessage->getPossibleIntentCollection();

                if ($collection->isNotEmpty()) {
                    $option =   JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                        | JSON_PRETTY_PRINT;

                    $mostPossible = $incomingMessage
                        ->getMostPossibleIntent();
                    $highlyPossible = $incomingMessage->getHighlyPossibleIntentNames();

                    if (isset($mostPossible)) {
                        $entities = $incomingMessage
                            ->getPossibleIntentEntities($mostPossible);
                    } else {
                        $entities = [];
                    }

                    $intent = $dialog->session->intentRepo->matchHighlyPossibleIntent($dialog->session);


                    $dialog->say()
                        ->info("NLU认为最可能的意图:\n$mostPossible")
                        ->info(
                            "NLU认为高可能的意图:"
                            . implode("\n- ", $highlyPossible)
                        )
                        ->info(
                            "NLU得到的所有意图: \n"
                            . $collection->toJson($option)
                        )
                        ->info(
                            "NLU得到的entities: \n"
                            . json_encode($entities, $option)
                        )
                        ->info(
                            "匹配得到的intent: \n"
                            . ($intent ? $intent->toPrettyJson() : '')
                        );
                } else {
                    $dialog->say()->info('没有命中任何意图.');
                }


                return $dialog->wait();
            });
    }

    public function __exiting(Exiting $listener): void
    {
    }


}