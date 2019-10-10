<?php


namespace Commune\Components\Story\Tasks;


use Closure;
use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Components\Predefined\Dialogue\HelpInt;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Story\Basic\AbsScriptTask;
use Commune\Components\Story\Intents\ChooseEpisodeInt;
use Commune\Components\Story\Intents\MenuInt;
use Commune\Components\Story\Intents\QuitGameInt;
use Commune\Components\Story\Intents\ReturnGameInt;
use Commune\Components\Story\Options\ScriptOption;

/**
 * Class ScriptMenu
 *
 * @property bool|null $restartEpisode
 */
class ScriptMenu extends AbsScriptTask
{
    public function __construct(string $scriptName)
    {
        parent::__construct($scriptName, $scriptName);
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onFulfill(function(Dialog $dialog){
            $dialog->say()->info($this->getScriptOption()->parseReplyId('quitGame'));

        });
    }

    public function goMenu(): Closure
    {
        return function(Dialog $dialog) : Navigator {
            return $dialog->goStage('menu');
        };
    }


    public function __hearing(Hearing $hearing): void
    {
        $commands = $this->getScriptOption()->commands;
        $hearing = $hearing
            // 菜单
            ->todo($this->goMenu())
                ->is($commands->menu)
                ->soundLike($commands->menu)
                ->isIntent(MenuInt::class)

            // 退出
            ->todo(Redirector::goFulfill())
                ->is($commands->quit)
                ->soundLike($commands->quit)
                ->isIntent(QuitGameInt::class)

            // 选择章节
            ->todo($this->todoChooseEpisode())
                ->is($commands->chooseEpisode)
                ->soundLike($commands->chooseEpisode)
                ->isIntent(ChooseEpisodeInt::class)

            // 返回游戏
            ->todo($this->todoReturnGame())
                ->is($commands->returnGame)
                ->soundLike($commands->returnGame)
                ->isIntent(ReturnGameInt::class)

            // 帮助
            ->todo($this->todoDescription())
                ->is($commands->help)
                ->soundLike($commands->help)
                ->isIntent(HelpInt::class)

            ->otherwise();

        parent::__hearing($hearing);
    }

    /**
     * 跳转走.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        $playing = $this->mem->playingEpisode;
        $option = $this->getScriptOption();

        $stage =  $stage->buildTalk()
            ->info(
                $option->parseReplyId('welcomeToScript'),
                [
                    'title' => $this->getScriptOption()->title
                ]
            );

        if (isset($playing)) {
            return $stage->info($option->parseReplyId('continuePlay'))
                ->goStage('playEpisode');
        }

        return $stage->goStage('chooseEpisode');

    }

    /**
     * 跳转到菜单.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onMenu(Stage $stage) : Navigator
    {
        $script = $this->getScriptOption();

        return $stage->buildTalk()
            ->askChoose(
                $script->parseReplyId('menu'),
                $this->getOperationMenu()
            )
            ->hearing()
            ->component([$this, 'runOperationFromMenu'])
            ->fallback($this->matchByEpisodeTitle($this->unlockEpisodes))
            ->end();
    }

    public function runOperationFromMenu(Hearing $hearing) : void
    {
        // 选择章节
        $hearing

        // 返回
        ->todo($this->todoReturnGame())
            ->isChoice('1')

        // 选择章节
        ->todo($this->todoChooseEpisode())
            ->isChoice('2')

        // 听取介绍
        ->todo($this->todoDescription())
            ->isChoice('3')

        // 查看结局
        ->todo($this->todoUnlockEndings())
            ->isChoice('4')

        // 退出醋
        ->todo($this->todoFulfill())
            ->isChoice('5')

        // 如果直接说出了章节名称.
        ->otherwise();
    }

    protected function getOperationMenu() : array
    {
        $commands = $this->getScriptOption()->commands;
        return [
            '1' => $commands->returnGame,
            '2' => $commands->chooseEpisode,
            '3' => $commands->help,
            '4' => $commands->unlockEndings,
            '5' => $commands->quit
        ];
    }

    protected function todoChooseEpisode() : callable
    {
        return Redirector::goStage('chooseEpisode');
    }

    protected function todoDescription() : callable
    {
        return Redirector::goStage('description');
    }

    protected function todoUnlockEndings() : callable
    {
        return Redirector::goStage('unlockEndings');
    }


    protected function todoReturnGame() : callable
    {
        return Redirector::goStage('playEpisode');
    }

    protected function todoFulfill() : callable
    {
        return Redirector::goFulfill();
    }

    /**
     * 选择章节.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onChooseEpisode(Stage $stage) : Navigator
    {
        $scriptOption = $this->getScriptOption();
        $episodes = $this->unlockEpisodes;

        // 与用户问答.
        return $stage->talk(
            // 告知用户已解锁的章节有哪些, 请选择.
            $this->askToChooseUnlockEpisode($scriptOption, $episodes),

            // 用户做出选择.
            $this->userChooseUnlockEpisode($scriptOption, $episodes)
        );
    }


    /**
     * 听取游戏介绍.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onDescription(Stage $stage) : Navigator
    {
        $commands = $this->getScriptOption()->commands;
        return $stage->buildTalk()
            ->info(
                $this->getScriptOption()->parseReplyId('description'),
                [
                    'suggestionStr' => implode(',', [
                        $commands->menu,
                        $commands->skip,
                        $commands->repeat,
                        $commands->backward,
                        $commands->restart,
                        $commands->quit])
                ]

            )
            ->goStage('menu');
    }


    /**
     * 确认开启一个章节.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onConfirmPlay(Stage $stage) : Navigator
    {
        $playing = $this->mem->playingEpisode;
        $title = $this->getScriptOption()->getEpisodeIdToTitles()[$playing] ?? '';
        return $stage
            ->buildTalk([
                'episode' => $title
            ])
            ->askConfirm(
                $this->getScriptOption()->parseReplyId('confirmPlay')
            )
            ->hearing()
            ->isPositive(function(Dialog $dialog){
                $this->restartEpisode = true;
                return $dialog->goStage('playEpisode');
            })
            ->isNegative(Redirector::goStage('chooseEpisode'))
            ->end();

    }

    /**
     * 进行游戏
     * @param Stage $stage
     * @return Navigator
     */
    public function __onPlayEpisode(Stage $stage) : Navigator
    {
        $episode = $this->mem->playingEpisode;
        $keepAlive = !($this->restartEpisode === true);
        $this->restartEpisode = null;

        if (empty($episode)) {
            return $stage->dialog->goStage('chooseEpisode');
        }

        $builder = $stage->onSubDialog(
            $this->mem->getId(),
            function() use ($episode){
                return new EpisodeTask(
                    $this->scriptName,
                    $episode
                );
            },
            null,
            $keepAlive
        );

        // 还可以指定返回到哪一个小节.
        $stageName = $this->mem->playingStage;
        $stageOption = $this->getScriptOption()->getStageOption($episode, $stageName);
        // 避免特殊情况下小节不属于章, 导致游戏无法再继续.
        if (isset($stageOption)) {
            $builder = $builder->onInit(Redirector::goStage($stageName));
        }

        return $builder->onQuit(Redirector::goStage('menu'))
                ->onBefore(function(Dialog $dialog) {
                    return $dialog->hear()
                        ->heardOrMiss();
                })
                ->end();
    }


    /**
     * @param Stage $stage
     * @return Navigator
     */
    public function __onUnlockEndings(Stage $stage) : Navigator
    {
        $endings = $this->mem->unlockEndings;
        $scriptOption = $this->getScriptOption();
        $stages = $scriptOption->getEndings($endings);

        if (empty($stages)) {
            return $stage->buildTalk()
                ->info($scriptOption->parseReplyId('noUnlockEndings'))
                ->goStage('menu');
        }

        $titles = array_map(function($stage){
            return $stage->title;
        }, $stages);

        return $stage->buildTalk()
            ->info(
                $scriptOption->parseReplyId('showUnlockEndings'),
                [
                    'titles' => implode(', ', $titles )
                ]
            )
            ->goStage('menu');
    }




    /*----------- actions -----------*/

    protected function matchByEpisodeTitle(array $episodeIds) : Closure
    {
        return function(Message $message, Dialog $dialog) use ($episodeIds): ? Navigator {

            $idToTitles = $this->getDef()
                ->getScriptOption()
                ->getEpisodeIdToTitles();

            $input = $message->getTrimmedText();

            foreach ($episodeIds as $episodeId) {
                $title = $idToTitles[$episodeId] ?? null;
                if (isset($title) && strstr($input, $title)) {
                    $this->mem->playingEpisode = $episodeId;
                    return $dialog->goStage('confirmPlay');
                }
            }

            return null;
        };
    }

    protected function isChoiceToPlayEpisode(string $episodeId) : Closure
    {
        return function(Dialog $dialog) use ($episodeId){
            $this->mem->playingEpisode = $episodeId;
            $this->restartEpisode = true;
            return $dialog->goStage('playEpisode');
        };
    }

    protected function askToChooseUnlockEpisode(ScriptOption $scriptOption, array $episodes) : Closure
    {
        return function(Dialog $dialog) use ($scriptOption, $episodes){

            $titles = [];
            foreach ($episodes as $index => $episode) {
                $episodeOption = $scriptOption->getEpisodeOption($episode);
                $titles[$episodeOption->option] = $episodeOption->title;
            }

            $dialog->say()
                ->askChoose(
                    $scriptOption->parseReplyId('chooseEpisode'),
                    $titles
                );

            return $dialog->wait();

        };
    }

    protected function userChooseUnlockEpisode(ScriptOption $scriptOption, array $episodes) : Closure
    {
        return function(Dialog $dialog, Message $message) use ($scriptOption, $episodes) {
            $builder = $dialog->hear($message);

            // choice 机制.
            foreach ($episodes as $index => $id) {
                $episodeOption = $scriptOption->getEpisodeOption($id);
                $builder = $builder
                    ->todo($this->isChoiceToPlayEpisode($id))
                        ->isChoice($episodeOption->option)
                        ->soundLike($episodeOption->option)
                        ->soundLikePart($episodeOption->title)
                    ->otherwise();
            }

            // fallback 机制.
            return $builder
                ->fallback($this->matchByEpisodeTitle($episodes))
                ->end();

        };
    }








}