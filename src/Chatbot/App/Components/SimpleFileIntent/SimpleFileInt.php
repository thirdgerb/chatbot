<?php


namespace Commune\Chatbot\App\Components\SimpleFileIntent;


use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsIntent;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class SimpleFileInt extends AbsIntent
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        parent::__construct([]);
    }


    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __onStart(Stage $stage) : Navigator
    {
        $option = $this->getDef()->getFileIntentOption();

        $stage = $stage
            ->build()
            ->info($option->content);

        $suggestions = $option->suggestions;
        if (empty($suggestions)) {
            return $stage->fulfill();
        }

        return $stage->goStage('suggest');
    }


    public function __onSuggest(Stage $stage) : Navigator
    {
        $option = $this->getDef()->getFileIntentOption();
        $suggestions = [
            '不用了' => function(Dialog $dialog) {
                return $dialog->fulfill();
            }
        ] + $option->suggestions;

        return $stage->component(new Menu(
            $option->question,
            $suggestions,
            function(
                string $context,
                Dialog $dialog,
                int $index
            ) {
                return $dialog->redirect->replaceTo($context);
            },
            function(Dialog $dialog) {
                return $dialog->fulfill();

            }
        ));
    }







    public static function __depend(Depending $depending): void
    {
    }


    public function __exiting(Exiting $listener): void
    {
    }

    /**
     * @return SimpleFileIntDefinition
     */
    public function getDef(): Definition
    {
        return IntentRegistrar::getIns()->get($this->getName());
    }


    public function __sleep(): array
    {
        $fields = parent::__sleep();
        $fields[] = 'name';
        return $fields;
    }

}