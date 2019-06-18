<?php


namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Psr\Log\LoggerInterface;

class LoadComponents implements Bootstrapper
{
    public function bootstrap(Application $app): void
    {
        $config = $app->getConfig();
        $logger = $app->getConsoleLogger();

        foreach ($config->components as $index => $name) {

            if (is_string($index) && is_array($name)) {
                $this->registerComponent($app, $logger, $index, $name);

            } elseif (is_int($index) && is_string($name)) {
                $this->registerComponent($app, $logger, $name);

            } else {
                $logger->warning(
                    "invalid component registration, key $index, "
                    . var_export($name, true)
                );
            }


        }
    }

    protected function registerComponent(
        Application $app,
        LoggerInterface $logger,
        string $clazz,
        array $data = []
    ) : void
    {
        if (!is_a($clazz, ComponentOption::class, TRUE)) {
            throw new ConfigureException("invalid component class $clazz");
        }

        $logger->debug("registering component $clazz");
        /**
         * @var ComponentOption $bootstrapper
         */
        $bootstrapper = new $clazz($data);
        $bootstrapper->bootstrap($app);
        $app->getReactorContainer()->instance($clazz, $bootstrapper);
    }

}