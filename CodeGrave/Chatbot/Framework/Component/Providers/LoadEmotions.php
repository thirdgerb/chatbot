<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Emotion\Feeling;

class LoadEmotions extends ServiceProvider
{
    protected $feelingExperience = [];


    public function addExperience(string $emotionName, $experience)  : void
    {
        $this->feelingExperience[$emotionName][] = $experience;
    }

    public function boot($app)
    {
        /**
         * @var Feeling $feels
         */
        $feels = $this->app->make(Feeling::class);

        foreach ($this->feelingExperience as $emotionName => $experiences) {
            foreach ($experiences as $experience) {
                $feels->experience($emotionName, $experience);
            }
        }
    }

    public function register()
    {
    }


}