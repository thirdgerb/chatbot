<?php


namespace Commune\Chatbot\App\Components\Configurable\Contexts;


use Commune\Chatbot\App\Components\Configurable\Actions\Factory;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 可配置的intent
 */
class ConfigurableIntent extends AbsIntent
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name, array $props = [])
    {
        $this->name = $name;
        parent::__construct($props);
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Configurable Intent 没有上下文
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        $def = $this->getDef();
        $config = $def->getIntentConfig();
        foreach ($config->actions as $action) {

            $navigator = $stage->dialog->app->callContextInterceptor(
                $this,
                Factory::make($def->getDomain(), $action)
            );

            if (isset($navigator)) {
                return $navigator;
            }
        }
        return $stage->dialog->fulfill();
    }


    public function __exiting(Exiting $listener): void
    {
        // 高级功能都不要用了. 用了还不如直接用类.
    }

    public static function __depend(Depending $depending): void
    {
        // 高级功能都不要用了. 用了还不如直接用类.
    }

    /**
     * @return ConfigurableIntentDef
     */
    public function getDef(): Definition
    {
        $repo = static::getRegistrar();
        $name = $this->getName();
        if ($repo->has($name)) {
            $def = $repo->get($name);
            if ($def instanceof ConfigurableIntentDef) {
                return $def;
            }
        }

        throw new ConfigureException(
            __METHOD__
            . ' intent name ' . $name
            . ' have not correctly preload yet'
        );
    }


    public function __sleep(): array
    {
        $fields = parent::__sleep();
        $fields[] = 'name';
        return $fields;
    }

}