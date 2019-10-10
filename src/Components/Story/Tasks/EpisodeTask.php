<?php


namespace Commune\Components\Story\Tasks;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Components\Predefined\Navigation\BackwardInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\RestartInt;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Directing\Reset\Repeat;
use Commune\Components\Story\Basic\AbsScriptTask;
use Commune\Components\Story\Basic\EpisodeDefinition;
use Commune\Components\Story\Intents\SkipInt;
use Commune\Components\Story\Options\ScriptOption;

/**
 * @method EpisodeDefinition getDef(): Definition
 * @property string $currentStage
 */
class EpisodeTask extends AbsScriptTask
{
    const STAGE_GOOD_ENDING = 'goodEnding';
    const STAGE_BAD_ENDING = 'badEnding';
    const STAGE_UNLOCK_EPISODE = 'unlockEpisode';

    const STAGES = [
        self::STAGE_GOOD_ENDING,
        self::STAGE_BAD_ENDING,
        self::STAGE_UNLOCK_EPISODE,
    ];

    /**
     * @var string
     */
    protected $episodeName;

    public function __construct(string $scriptName, string $episodeName)
    {
        $this->episodeName = $episodeName;
        parent::__construct(
            $scriptName,
            ScriptOption::makeEpisodeName($scriptName, $episodeName)
        );
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __staging(Stage $stage) : void
    {
        // 在 episodeDefinition 里再赋值.
        $this->mem->playingStage = null;
    }

    /*--------- stages ----------*/

    public function __onStart(Stage $stage): Navigator
    {
        $option = $this->getDef()->getEpisodeOption();
        $stages = $option->stages;
        $first = $stages[0];
        $to = $first->id;

        return $stage->buildTalk()
            ->info(
                $this->getScriptOption()->parseReplyId('startEpisode'),
                [
                    'option' => $option->option,
                    'title' => $option->title,
                ]
            )
            ->goStage($to);
    }

    /**
     * 坏结局, 要不要重启本章.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onBadEnding(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm($this->getScriptOption()->parseReplyId('badEnding'))
            ->hearing()
            // 重新开始.
            ->isPositive(Redirector::goRestart())
            // 退出子会话
            ->isNegative(Redirector::goQuit())
            ->end();
    }

    /**
     * 好结局, 游戏结束.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onGoodEnding(Stage $stage) : Navigator
    {
        $this->mem->playingEpisode = null;
        return $stage->buildTalk()
            ->info(
                $this->getScriptOption()->parseReplyId('goodEnding')
            )
            ->action(Redirector::goQuit());

    }

    /**
     * 前往解锁的章节.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onUnlockEpisode(Stage $stage) : Navigator
    {
        $unlockingEpisode = $this->mem->unlockingEpisode;
        $title = $this->getScriptOption()->getEpisodeIdToTitles()[$unlockingEpisode] ?? '';
        return $stage->buildTalk(['episode' => $title])
            ->askConfirm(
                $this->getScriptOption()->parseReplyId('askStartNewEpisode')
            )
            ->hearing()
            ->isPositive($this->goEpisode($unlockingEpisode))
            ->isNegative(Redirector::goQuit())
            ->end();
    }

    /*--------- private ----------*/

    public function __sleep(): array
    {
        $names = parent::__sleep();
        $names[] = 'episodeName';
        return $names;
    }

}