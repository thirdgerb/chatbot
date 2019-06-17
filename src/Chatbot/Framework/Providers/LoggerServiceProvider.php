<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Framework\Conversation\ConversationLogger;
use Commune\Container\ContainerContract;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Monolog\Logger as Monolog;

class LoggerServiceProvider extends BaseServiceProvider
{

    public function boot($app): void
    {
    }

    public function register(): void
    {
        $this->app->singleton(
            LoggerInterface::class,
            function($app) {

                /**
                 * @var ChatbotConfig $chatbotConfig
                 */
                $chatbotConfig = $app[ChatbotConfig::class];
                $config = $chatbotConfig->logger;

                $level = Monolog::toMonologLevel($config->level);

                if ($config->days > 0) {
                    $handler = new RotatingFileHandler(
                        $config->path,
                        $config->days,
                        $level,
                        $config->bubble,
                        $config->permission,
                        $config->locking
                    );
                } else {
                    $handler = new StreamHandler(
                        $config->path,
                        $level,
                        $config->bubble,
                        $config->permission,
                        $config->locking
                    );
                }

                $logger = new Monolog($config->name, [
                    $handler
                ]);

                return new ConversationLogger(
                    $logger,
                    $app
                );

            }
        );
    }


}