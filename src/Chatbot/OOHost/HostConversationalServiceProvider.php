<?php


namespace Commune\Chatbot\OOHost;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Session\Repository;
use Commune\Chatbot\OOHost\Session\RepositoryImpl;
use Commune\Container\ContainerContract;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Hearing\HearingHandler;
use Commune\Chatbot\OOHost\Session\Driver;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;
use Illuminate\Support\Arr;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Conversation\User;

class HostConversationalServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
    }

    public function register()
    {
        $this->registerDefaultSlots();
        $this->registerHearing();
        $this->registerRepository();
        $this->registerSession();
    }


    protected function registerDefaultSlots() : void
    {
        $this->app->singleton(
            Speech::DEFAULT_SLOTS,
            function(Conversation $conversation){

                $env = $conversation->getChatbotConfig()->host->slots;
                $slots = Arr::dot($env);

                /**
                 * @var User $user
                 */
                $user = $conversation[User::class];
                $slots[Speech::SLOT_USER_NAME] = $user->getName();

                return $slots;
            }
        );
    }

    protected function registerSession() : void
    {
        $this->app->bind(Session::class, function($conversation, $parameters){
            return new SessionImpl(
                $parameters[Session::SESSION_ID_VAR],
                $conversation[ChatbotConfig::class]->host,
                $conversation,
                $conversation[Repository::class]
            );
        });
    }

    protected function registerRepository() : void
    {
        $this->app->singleton(Repository::class, function($app)
        {
            /**
             * @var Conversation $conversation
             */
            $conversation = $app[Conversation::class];

            return new RepositoryImpl(
                $conversation->getTraceId(),
                $app[Driver::class]
            );

        });
    }

    protected function registerHearing() : void
    {
        // 可以重写成自己觉得合适的
        $this->app->bind(
            Hearing::class,
            function($app, $parameters){
                return new HearingHandler(
                    $parameters['context'],
                    $parameters['dialog'],
                    $parameters['message']
                );
            }
        );
    }



}