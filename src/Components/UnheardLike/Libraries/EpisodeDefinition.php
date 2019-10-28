<?php


namespace Commune\Components\UnheardLike\Libraries;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\ContextDefinition;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Components\UnheardLike\Contexts\EpisodeTask;
use Commune\Components\UnheardLike\Options\Episode;

class EpisodeDefinition extends ContextDefinition implements Definition
{
    /**
     * @var Episode
     */
    protected $episode;

    public function __construct(
        Episode $episode

    )
    {
        $this->episode = $episode;
        parent::__construct(
            $episode->id,
            EpisodeTask::class,
            $episode->title,
            null
        );
    }

    /*--------- getter --------*/

    public function getEpisode() : Episode
    {
        return $this->episode;
    }


    public function newContext(...$args): Context
    {
        return new EpisodeTask($this->getName());
    }
}