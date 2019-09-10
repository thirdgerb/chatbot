<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\Contextual\AskEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ChooseEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ChooseIntent;
use Commune\Chatbot\App\Messages\QA\Contextual\ConfirmIntent;
use Commune\Chatbot\App\Messages\QA\Contextual\SelectEntity;
use Commune\Chatbot\App\Messages\QA\Selects;
use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\App\Messages\Templates\ConfirmTemp;
use Commune\Chatbot\App\Messages\Templates\MissedTemp;
use Commune\Chatbot\App\Messages\Templates\QuestionTemp;
use Commune\Chatbot\App\Messages\Templates\QuitTemp;
use Commune\Chatbot\App\Messages\Templates\TranslateTemp;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Framework\Conversation\RendererImpl;
use Commune\Chatbot\App\Messages\System\QuitSessionReply;
use Commune\Chatbot\App\Messages\System\MissedReply;

/**
 * register default reply message renderer
 * and default template for questions
 */
class ReplyRendererServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    protected $templates = [

        // default
        Renderer::DEFAULT_ID => TranslateTemp::class,

        // base question
        VbQuestion::REPLY_ID => QuestionTemp::class,
        Confirm::REPLY_ID => ConfirmTemp::class,
        Choose::REPLY_ID => QuestionTemp::class,
        Selects::REPLY_ID => QuestionTemp::class,

        // intent question
        AskEntity::REPLY_ID => QuestionTemp::class,
        ConfirmIntent::REPLY_ID => ConfirmTemp::class,
        ChooseIntent::REPLY_ID => QuestionTemp::class,
        SelectEntity::REPLY_ID => QuestionTemp::class,
        ChooseEntity::REPLY_ID => QuestionTemp::class,
        SelectEntity::REPLY_ID => QuestionTemp::class,


        // event
        QuitSessionReply::REPLY_ID => QuitTemp::class,
        MissedReply::REPLY_ID => MissedTemp::class,

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