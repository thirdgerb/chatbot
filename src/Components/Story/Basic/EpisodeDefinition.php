<?php


namespace Commune\Components\Story\Basic;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Components\Predefined\Navigation\BackwardInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\RepeatInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\RestartInt;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Context\Helpers\ContextCaller;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Story\Intents\SkipInt;
use Commune\Components\Story\Options\ChoiceOption;
use Commune\Components\Story\Options\EpisodeOption;
use Commune\Components\Story\Options\ScriptOption;
use Commune\Components\Story\Options\StageOption;
use Commune\Components\Story\Tasks\EpisodeTask;
use Psr\Log\LoggerInterface;

class EpisodeDefinition implements Definition
{
    use ContextCaller;

    /**
     * @var ScriptOption
     */
    protected $script;

    /**
     * @var EpisodeOption
     */
    protected $episode;

    /**
     * @var StageOption[]
     */
    protected $stageOptions = [];

    /**
     * EpisodeDefinition constructor.
     * @param ScriptOption $script
     * @param EpisodeOption $episode
     */
    public function __construct(ScriptOption $script, EpisodeOption $episode)
    {
        $this->script = $script;
        $this->episode = $episode;

        foreach ($this->episode->stages as $stage) {
            $this->stageOptions[$stage->id] = $stage;
        }
    }

    /**
     * @return ScriptOption
     */
    public function getScriptOption(): ScriptOption
    {
        return $this->script;
    }

    public function getScriptName() : string
    {
        return $this->script->id;
    }

    /**
     * @return EpisodeOption
     */
    public function getEpisodeOption(): EpisodeOption
    {
        return $this->episode;
    }


    public function newContext(...$args): Context
    {
        $class = $this->getClazz();
        if (!is_a($class, EpisodeTask::class, TRUE)) {
            throw new ConfigureException(
                "episode class $class is invalid"
            );
        }

        return new $class($this->getScriptName(), $this->getName());
    }

    public function getName(): string
    {
        return $this->script->parseEpisodeId($this->episode->id);
    }

    public function getClazz(): string
    {
        return $this->episode->class;
    }

    public function getDesc(): string
    {
        return $this->episode->title;
    }

    public function getTags(): array
    {
        return [];
    }

    public function addEntity(Entity $entity): void
    {
        throw new ConfigureException('episode of story component should not define any entity');
    }

    public function hasEntity(string $entityName): bool
    {
        return false;
    }

    public function getEntity(string $entityName): ? Entity
    {
        return null;
    }

    public function getEntityNames(): array
    {
        return [];
    }

    public function getEntities(): array
    {
        return [];
    }

    public function dependsEntities(Context $instance): array
    {
        return [];
    }

    public function dependingEntity(Context $instance): ? Entity
    {
        return null;
    }

    public function hasStage(string $stageName): bool
    {
        return $stageName === Context::INITIAL_STAGE
            || in_array($stageName, EpisodeTask::STAGES)
            || array_key_exists($stageName, $this->stageOptions);
    }

    public function setStage(string $stage, callable $builder): void
    {
        $name = $this->getName();
        throw new ConfigureException(static::class . " of $name should not set any stage caller");
    }

    public function getStageNames(): array
    {
        return array_keys($this->stageOptions);
    }

    public function getStageOption(string $stage) : StageOption
    {
        return $this->stageOptions[$stage] ?? null;
    }

    protected function getStageCaller(string $stageName): callable
    {
        if ($stageName === Context::INITIAL_STAGE) {
            return function(Stage $stage) : Navigator {
                return $stage->self->__onStart($stage);
            };
        }

        if (in_array($stageName, EpisodeTask::STAGES)) {
            return function(Stage $stage) use ($stageName): Navigator {
                return call_user_func(
                    [$stage->self, Context::STAGE_METHOD_PREFIX . ucfirst($stageName)],
                    $stage
                );
            };
        }

        $option = $this->getStageOption($stageName);

        return function(Stage $stage) use ($option) : Navigator {

            /**
             * @var EpisodeTask $episodeTask
             */
            $episodeTask = $stage->self;
            $episodeTask->mem->playingStage = $stage->name;

            return
                // 优先中间件
                $this->runStageMiddleware($stage, $option)
                // 优先走一下hearing
                ?? $this->runStageHearing($stage, $option)
                // 获取道具环节.
                ?? $this->runStagegetItem($stage, $option)
                // 读故事文本.
                ?? $this->runStageStories($stage, $option)
                // 查看是否检索了章节.
                ?? $this->runStageUnlockEpisodes($stage, $option)
                ?? $this->runStageRedirection($stage, $option)
                ?? $this->runStageConfirm($stage, $option)
                ?? $this->runStageChoose($stage, $option)
                ?? $this->runStageIsEnding($stage);
        };
    }

    protected function runStageHearing(Stage $stage, StageOption $option) : ? Navigator
    {
        if ($stage->isCallback()) {
            $hearing = $stage->dialog->hear();
            $commands = $this->getScriptOption()->commands;
            $hearing = $hearing
                ->runIntent(BackwardInt::class)
                ->runIntent(RepeatInt::class)
                ->runIntent(RestartInt::class)
                    ->todo(Redirector::goRewind())
                    ->is($commands->skip)
                    ->isIntent(SkipInt::class)
                ->otherwise();

            return $hearing->navigator;
        }
        return null;
    }

    protected function runStageMiddleware(Stage $stage, StageOption $option) : ? Navigator
    {
        // middleware
        $middleware = $option->middleware;
        if (!empty($middleware)) {
            foreach ($middleware as $name) {
                if (!class_exists($name)) {
                    throw new ConfigureException(
                        __METHOD__
                        . " failed, episode "
                        . $this->getName()
                        . " got invalid middleware $name for stage "
                        . $stage->name
                    );
                }

                $pipe = new $name($stage);
                $navigator = $pipe();

                if ($navigator instanceof Navigator) {
                    return $navigator;
                }
            }
        }
        return null;
    }



    protected function runStageStories(Stage $stage, StageOption $option) : ? Navigator
    {
        if (!$stage->isStart()) {
            return null;
        }
        // 发布story
        $stories = $option->stories;
        if (!empty($stories)) {
            $speech = $stage->dialog->say($this->getEpisodeSlots());
            foreach ($stories as $replyId) {
                $speech->info($this->script->parseReplyId($replyId));
            }
        }
        return null;
    }

    protected function getEpisodeSlots() : array
    {
        $scriptSlots = $this->script->defaultSlots;
        $episodeSlots = $this->episode->defaultSlots;
        return $episodeSlots + $scriptSlots;
    }


    protected function runStagegetItem(Stage $stage, StageOption $option) : ? Navigator
    {
        if (!$stage->isStart()) {
            return null;
        }

        $getItem = $option->getItem;
        if (empty($getItem)) {
            return null;
        }

        /**
         * @var EpisodeTask $self
         */
        $self = $stage->self;
        $scriptMem = ScriptMem::from($self);
        $this->recordItems($scriptMem, $getItem, $stage->dialog->logger);

        return null;
    }

    protected function recordItems(ScriptMem $mem, array $getItem, LoggerInterface $logger) : void
    {
        $items = $mem->items;
        $itemDefs = $this->script->itemDef;

        $availableItems = [];
        foreach ($itemDefs as $itemDef) {
            $availableItems[$itemDef->id] = $itemDef->enums;
        }

        foreach ($getItem as $name => $value) {
            if (
                array_key_exists($name, $availableItems)
                && in_array($value, $availableItems[$name])
            ) {
                $items[$name] = $value;

            } else {
                $logger->error(
                    __METHOD__
                    . " try to set invalid item name $name or value $value"
                );
            }
        }
        $mem->items = $items;


    }


    protected function runStageUnlockEpisodes(Stage $stage, StageOption $option) : ? Navigator
    {
        if (!$stage->isStart()) {
            return null;
        }

        // 解锁的章节
        $unlockEpisode = $option->unlockEpisode;
        if (empty($unlockEpisode)) {
            return null;
        }

        // 解锁章节标记.
        $scriptMem = ScriptMem::from($stage->self);
        $episodes = $scriptMem->unlockEpisodes;

        // 赋值当前
        $scriptMem->unlockingEpisode = $unlockEpisode;

        // 没有解锁过的话, 会提示解锁新篇章.
        if (!in_array($unlockEpisode, $episodes)) {

            $episodes[] = $unlockEpisode;
            $scriptMem->unlockEpisodes = $episodes;
            $episode = $this->script->getEpisodeOption($unlockEpisode);

            if (!isset($episode)) {

                $stage->self
                    ->getSession()
                    ->logger
                    ->error(__METHOD__ . " unlock episode $unlockEpisode not exists");

                return null;
            }

            $title = $episode->title;

            // 新解锁章节.
            $stage->dialog->say()->info(
                $this->script->parseReplyId('unlockEpisode'),
                ['episode' => $title]
            );

        }

        return $stage->dialog->goStage(EpisodeTask::STAGE_UNLOCK_EPISODE);
    }


    protected function runStageRedirection(Stage $stage, StageOption $option) : ? Navigator
    {
        if (!$stage->isStart()) {
            return null;
        }

        $redirects = $option->redirects;
        if (empty($redirects)) {
            return null;
        }

        $scriptMem = ScriptMem::from($stage->self);
        $items = $scriptMem->items;
        foreach ($redirects as $redirect) {

            $condition = $redirect->ifItem;
            if (!$this->checkCondition($items, $condition)) {
                continue;
            }
            // stage name
            $to = $redirect->to;
            return $stage->dialog->goStage($to);
        }

        return null;
    }

    protected function runStageConfirm(Stage $stage, StageOption $option) : ? Navigator
    {
        $confirms = $option->confirms;
        if (empty($confirms)) {
            return null;
        }

        $scriptMem = ScriptMem::from($stage->self);
        $items = $scriptMem->items;
        foreach ($confirms as $confirm) {
            $condition = $confirm->ifItem;
            if (!$this->checkCondition($items, $condition)) {
                continue;
            }

            return $stage->buildTalk()
                ->askConfirm($this->script->parseReplyId($confirm->query))
                ->hearing()
                ->isPositive(Redirector::goStage($confirm->yes))
                ->isNegative(Redirector::goStage($confirm->no))

                ->fallback(function(Dialog $dialog){
                    $dialog->say()->warning($this->script->parseReplyId('needConfirmation'));
                    return $dialog->wait();
                })
                ->end();
        }

        return null;
    }

    protected function runStageChoose(Stage $stage, StageOption $option) : ? Navigator
    {
        $choose = $option->choose;
        if (empty($choose)) {
            return null;
        }

        $scriptMem = ScriptMem::from($stage->self);
        $items = $scriptMem->items;

        foreach ($choose as $chooseOption) {

            $condition = $chooseOption->ifItem;
            if (!$this->checkCondition($items, $condition)) {
                continue;
            }


            // 给用户的选项
            $choices = [];
            // 给系统的选项.
            $options = [];
            $i = 0;
            foreach ($chooseOption->choices as $choice) {
                $i ++;
                $option = empty($choice->option) ? $i : $choice->option;
                $choices[$option] = $choice->id;
                $options[$option] = $choice;
            }

            $hearing = $stage->buildTalk()
                ->askChoose(
                    $this->script->parseReplyId($chooseOption->query),
                    $choices
                )
                ->hearing();

            foreach ($options as $option => $choice) {

                /**
                 * @var ChoiceOption $choice
                 */
                $hearing = $hearing->todo(function(Context $self, Dialog $dialog) use ($choice) : ? Navigator{
                    $ifItem = $choice->ifItem;
                    $mem = ScriptMem::from($self);
                    $items = $mem->items;

                    if (!$this->checkCondition($items, $ifItem)) {
                        return null;
                    }

                    $getItem = $choice->getItem;
                    if (!empty($getItem)) {
                        $this->recordItems($mem, $getItem, $dialog->logger);
                    }

                    $to = $choice->to;
                    return $dialog->goStage($to);
                })
                    ->isChoice($option)
                    ->soundLike($option)
                    ->soundLikePart($choice->id);

                $intent = $choice->intent;
                if (!empty($intent)) {
                    $hearing = $hearing->isIntent($intent);
                }

                $hearing = $hearing->otherwise();
            }

            // 选项不存在
            $hearing->fallback(function(Dialog $dialog) : Navigator {

                $dialog->say()->warning(
                    $this->script->parseReplyId('choiceNotExists')
                );

                return $dialog->rewind();
            });

            return $hearing->end();
        }

        return null;
    }

    protected function checkCondition(array $items, array $conditions) : bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $key => $value) {

            if (!isset($items[$key])) {
                return false;
            }

            if ($items[$key] != $value) {
                return false;
            }

        }

        return true;
    }


    protected function runStageIsEnding(Stage $stage) : Navigator
    {
        $stageOption = $this->episode->getStageOption($stage->name);

        $scriptMem = ScriptMem::from($stage->self);
        $endings = $scriptMem->unlockEndings;
        $endingName = ScriptOption::makeEndingName($this->episode, $stageOption);

        $endings[] = $endingName;
        $scriptMem->unlockEndings = array_unique($endings);


        $builder = $stage->buildTalk();

        // 告知解锁结局.
        $replyId = $this->script->parseReplyId('unlockEnding');
        $builder->info(
            $replyId,
            [
                'ending' => $stageOption->title
            ]
        );


        $toEnding = $stageOption->isGoodEnding != 0
            ? 'goodEnding'
            : 'badEnding';

        return $builder
            ->info($this->script->parseReplyId('endEpisode'))
            ->goStage($toEnding);
    }

}
