<?php


namespace Commune\Components\Story\Basic;

use Closure;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
use Commune\Chatbot\OOHost\Context\AbsContext;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Components\Story\Options\ScriptOption;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
use Commune\Components\Story\Tasks\EpisodeTask;

/**
 * @property-read string $scriptName 脚本的名称.
 * @property-read ScriptMem $mem 脚本的存储.
 * @property-read string[] $unlockEpisodes 当前用户解锁的章节.
 */
abstract class AbsScriptTask extends AbsContext implements HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * @var string
     */
    protected  $_scriptName;

    /**
     * @var string
     */
    protected  $_contextName;

    /**
     * @var ScriptMem|null
     */
    protected  $_scriptMem;

    /**
     * @var ScriptOption
     */
    protected  $_scriptOption;

    /**
     * AbsScriptTask constructor.
     * @param string $scriptName
     * @param string $contextName
     */
    public function __construct(string $scriptName, string $contextName)
    {
        $this->_scriptName = $scriptName;
        $this->_contextName = $contextName;
        parent::__construct([]);
    }

    public function __hearing(Hearing $hearing) : void
    {
        $commands = $this->getScriptOption()->commands;

        // 默认回复.
        $hearing->fallback(function(Dialog $dialog) use ($commands){

                // 提示指令.
                $dialog->say()
                    ->info(
                        $this->getScriptOption()
                            ->parseReplyId('helpNotice'),
                        [
                            'suggestionStr' => implode(',', [
                                $commands->menu,
                                $commands->skip,
                                $commands->repeat,
                                $commands->backward,
                                $commands->restart,
                            ])
                        ]
                    );

                return $dialog->rewind();
            });
    }

    /*----------- stages -----------*/

    public function goEpisode(string $episode) : Closure
    {
        return function(Dialog $dialog) use ($episode){
            $this->mem->playingEpisode = $episode;
            return $dialog->redirect->replaceTo(
                new EpisodeTask(
                    $this->scriptName,
                    $episode
                ),
                Redirect::THREAD_LEVEL
            );

        };
    }

    /*----------- as context -----------*/


    public function getName(): string
    {
        return $this->_contextName;
    }

    /**
     * @return ScriptDefinition
     */
    public function getDef(): Definition
    {
        $name = $this->getName();
        $def = $this->getRegistrar()->getDef($name);

        if (empty($def)) {
            throw new RuntimeException("script definition $name not exists");
        }

        return $def;
    }

    public function getRegistrar() : StoryRegistrar
    {
        $this->hasInstanced();

        $subs = $this->getSession()
            ->contextRepo
            ->getSubRegistrars();
        $registrar = $subs[StoryRegistrar::class] ?? null;

        if (empty($registrar)) {
            throw new ConfigureException(
                "registrar " .StoryRegistrar::class .' not register to ContextRegistrar'
            );
        }

        return $registrar;
    }


    public function __sleep(): array
    {
        $sleeps = parent::__sleep();
        $sleeps[] = '_scriptName';
        $sleeps[] = '_contextName';
        return $sleeps;
    }

    public function getId(): string
    {
        return $this->_contextId ?? $this->_contextId = $this->createUuId();
    }

    public function toInstance(Session $session): SessionInstance
    {
        if (isset($this->_session)) {
            return $this;
        }
        $this->_session = $session;
        $this->assign();
        $this->_session->repo->cacheSessionData($this);
        return $this;
    }

    /*----------- mutator -----------*/


    public function __getMem() : ScriptMem
    {
        return $this->_scriptMem
            ?? $this->_scriptMem = ScriptMem::from($this);

    }

    public function getScriptOption() : ScriptOption
    {
        return $this->_scriptOption
            ?? $this->_scriptOption = $this->getRegistrar()
                ->getScriptOption($this->_scriptName);
    }


    public function __getScriptName() : string
    {
        return $this->_scriptName;
    }


    /**
     * @return string[]
     */
    public function __getUnlockEpisodes()  : array
    {
        // 准备已解锁章节的数据.
        $mem = $this->mem;
        $unlockEpisodes = $mem->unlockEpisodes;
        $scriptOption = $this->getDef()->getScriptOption();
        $defaultEpisodes = $scriptOption->defaultEpisodes;
        /**
         * @var String[] $episodes
         */
        $episodes = array_unique(array_merge($defaultEpisodes, $unlockEpisodes));
        return $episodes;
    }

}