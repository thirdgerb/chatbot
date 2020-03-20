<?php


namespace Commune\Components\Story\Basic;


use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Components\Story\Options\ScriptOption;

interface StoryRegistrar extends ContextRegistrar
{
    public function registerScriptOption(ScriptOption $option) : void;

    public function getEpisodeDef(string $episodeName) : ? EpisodeDefinition;

    public function getScriptDef(string $scriptName) : ? ScriptDefinition;

    public function getScriptOption(String $scriptName) : ? ScriptOption;
}