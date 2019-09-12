<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\App\Messages\QA;
use Commune\Chatbot\App\Messages\Templates;
use Commune\Chatbot\App\Messages\System;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Framework\Conversation\RendererImpl;

/**
 * register default reply message renderer
 * and default template for questions
 */
class ReplyRendererServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    protected $templates = [

        // default
        Renderer::DEFAULT_ID => Templates\TranslateTemp::class,

        // base question
        QA\VbQuestion::REPLY_ID => Templates\QuestionTemp::class,
        QA\Confirm::REPLY_ID => Templates\ConfirmTemp::class,
        QA\Choose::REPLY_ID => Templates\QuestionTemp::class,
        QA\Selects::REPLY_ID => Templates\QuestionTemp::class,

        // intent question
        QA\Contextual\AskEntity::REPLY_ID => Templates\QuestionTemp::class,
        QA\Contextual\ConfirmIntent::REPLY_ID => Templates\ConfirmTemp::class,
        QA\Contextual\ConfirmEntity::REPLY_ID => Templates\ConfirmTemp::class,
        QA\Contextual\ChooseIntent::REPLY_ID => Templates\QuestionTemp::class,
        QA\Contextual\ChooseEntity::REPLY_ID => Templates\QuestionTemp::class,
        QA\Contextual\SelectEntity::REPLY_ID => Templates\QuestionTemp::class,


        // event
        System\QuitSessionReply::REPLY_ID => Templates\QuitTemp::class,
        System\MissedReply::REPLY_ID => Templates\MissedTemp::class,

    ];

    /**
     * @param \Commune\Container\ContainerContract $app
     */
    public function boot($app)
    {
        /**
         * @var Renderer $renderer
         */
        $renderer = $app->get(Renderer::class);

        // default binding.
        foreach ($this->templates as $id => $tempId) {
            $renderer->bindTemplate($id, $tempId);
        }

    }

    public function register()
    {
        $this->app->singleton(Renderer::class, function($app){
            /**
             * @var Application $chatApp
             */
            $chatApp = $app[Application::class];
            return new RendererImpl($chatApp->getProcessContainer());
        });
    }


}