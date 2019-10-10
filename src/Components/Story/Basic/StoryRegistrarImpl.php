<?php


namespace Commune\Components\Story\Basic;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\ContextRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Components\Story\Options\EpisodeOption;
use Commune\Components\Story\Options\ScriptOption;
use Commune\Components\Story\Options\StageOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Illuminate\Support\Str;

class StoryRegistrarImpl extends ContextRegistrarImpl implements StoryRegistrar
{
    /**
     * @var ScriptOption[]
     */
    protected $scripts = [];

    /**
     * @var ScriptDefinition[]
     */
    protected $scriptDefinitions = [];

    /**
     * @var string[]
     */
    protected $episodeToScript = [];


    protected function registerSelfToParent(ContextRegistrar $parent): void
    {
        $parent->addSubRegistrar(StoryRegistrar::class, $this);
    }

    public function registerScriptOption(ScriptOption $option): void
    {
        $id = $option->id;
        $this->validateScriptOption($option);

        $this->scripts[$id] = $option;
        foreach ($option->episodes as $episode) {
            $name = $option->parseEpisodeId($episode->id);
            $this->episodeToScript[$name] = $id;
        }
    }

    public function validateScriptOption(ScriptOption $option) : void
    {
        // episode
        $episodeNames = [];
        foreach ($option->episodes as $episode) {
            $episodeNames[] = $episode->id;
        }

        // validate
        foreach ($option->episodes as $episode) {
            // stageNames
            $stageNames = [];
            foreach ($episode->stages as $stage) {
                $stageNames[] = $stage->id;
            }

            foreach ($episode->stages as $stage) {
                // confirms
                foreach ($stage->confirms as $confirm) {

                    if (!$this->validateItems($option, $confirm->ifItem)) {
                        $this->validateError('invalid confirm ifItem', $option, $episode, $stage);
                    }

                    if (!in_array($confirm->yes, $stageNames)) {
                        $this->validateError('invalid confirm yes', $option, $episode, $stage);
                    }

                    if (!in_array($confirm->no, $stageNames)) {
                        $this->validateError('invalid confirm no', $option, $episode, $stage);
                    }

                }

                // getItem
                if (!$this->validateItems($option, $stage->getItem)) {
                    $this->validateError('invalid getItem', $option, $episode, $stage);
                }

                $unlockEpisode = $stage->unlockEpisode;
                if (!empty($unlockEpisode) && !in_array($unlockEpisode, $episodeNames)) {
                    $this->validateError('invalid unlock episodes', $option, $episode, $stage);
                }

                // redirects
                foreach ($stage->redirects as $redirect) {
                    if (!$this->validateItems($option, $redirect->ifItem)) {
                        $this->validateError('invalid redirect ifItem', $option, $episode, $stage);
                    }

                    if (!in_array($redirect->to, $stageNames)) {
                        $this->validateError('invalid redirect to', $option, $episode, $stage);
                    }

                }

                // choose
                foreach ($stage->choose as $choose) {

                    if (!$this->validateItems($option, $choose->ifItem)) {
                        $this->validateError('invalid choose', $option, $episode, $stage);
                    }

                    foreach ($choose->choices as $choice) {
                        if (!$this->validateItems($option, $choice->ifItem)) {
                            $this->validateError('invalid choose ifItems', $option, $episode, $stage);
                        }

                        if (!$this->validateItems($option, $choice->getItem)) {
                            $this->validateError('invalid items ifItems', $option, $episode, $stage);
                        }


                        if (!in_array($choice->to, $stageNames)
                        ) {
                            $this->validateError('invalid choice', $option, $episode, $stage);
                        }

                    }
                }
            }
        }
    }

    protected function validateItems(ScriptOption $script, array $items) : bool
    {
        if (empty($items)) {
            return true;
        }

        // items
        $itemMap = [];
        foreach ($script->itemDef as $item) {
            $itemMap[$item->id] = $item->enums;
        }

        foreach ($items as $key => $value) {
            if (!isset($itemMap[$key]) || !in_array($value, $itemMap[$key])) {
                return false;
            }
        }

        return true;
    }

    protected function validateError(
        string $message,
        ScriptOption $script,
        EpisodeOption $episode,
        StageOption $stage = null
    ) : void
    {
        $str = 'script:'
            . $script->id
            . ' episode:'
            . $episode->id;

        if (isset($stage)) {
            $str .= ' stage:' . $stage->id;
        }

        $str .= ' config failed, '. $message;
        throw new ConfigureException(
            __METHOD__
            . ' '
            . $str
        );
    }

    public function registerDef(Definition $def, bool $force = false): bool
    {
        throw new ConfigureException(
            static::class
            . ' do not register any Context Definition instance'
        );
    }

    public function hasDef(string $contextName): bool
    {
        return array_key_exists($contextName, $this->scripts)
            || array_key_exists($contextName, $this->episodeToScript);

    }

    public function getDef(string $contextName): ? Definition
    {
        return $this->getEpisodeDef($contextName)
            ?? $this->getScriptDef($contextName);
    }

    public function getEpisodeDef(string $episodeName) : ? EpisodeDefinition
    {
        if (!array_key_exists($episodeName, $this->episodeToScript)) {
            return null;
        }

        $scriptName = $this->episodeToScript[$episodeName];
        $scriptOption = $this->scripts[$scriptName];
        foreach ($scriptOption->episodes as $episode) {
            $episodeId = $scriptOption->parseEpisodeId($episode->id);
            if ($episodeName === $episodeId) {
                return new EpisodeDefinition($scriptOption, $episode);
            }
        }

        throw new ConfigureException(
            __METHOD__
            . " episode name $episodeName exists but definition not found"
        );
    }

    /**
     * ScriptDef 会被持有.
     * @param string $scriptName
     * @return ScriptDefinition|null
     */
    public function getScriptDef(string $scriptName) : ? ScriptDefinition
    {
        if (isset($this->scriptDefinitions[$scriptName])) {
            return $this->scriptDefinitions[$scriptName];
        }

        if (!array_key_exists($scriptName, $this->scripts)) {
            return null;
        }

        $option = $this->scripts[$scriptName];
        return $this->scriptDefinitions[$scriptName] = new ScriptDefinition($option);
    }


    public function eachDef(): \Generator
    {
        foreach ($this->scripts as $name => $scriptOption) {
            yield $this->getScriptDef($name);
            foreach ($scriptOption->episodes as $episode) {
                yield new EpisodeDefinition($scriptOption, $episode);
            }
        }
    }

    public function countDef(): int
    {
        return count($this->scripts) + count($this->episodeToScript);
    }

    public function getDefNamesByDomain(string $domain = ''): array
    {
        if (empty($domain)) {
            return array_merge(array_keys($this->scripts), array_keys($this->episodeToScript));
        }

        return array_merge(
            array_filter(array_keys($this->scripts), function($scriptName) use ($domain) {
                return Str::startsWith($scriptName, $domain);
            }),

            array_filter(array_keys($this->episodeToScript), function($episodeName) use ($domain) {
                return Str::startsWith($episodeName, $domain);
            })
        );
    }

    public function getDefNamesByTag(string ...$tags): array
    {
        return [];
    }

    public function getPlaceholderDefNames(): array
    {
        return [];
    }

    public function getScriptOption(String $scriptName): ? ScriptOption
    {
        return $this->scripts[$scriptName] ?? null;
    }


}