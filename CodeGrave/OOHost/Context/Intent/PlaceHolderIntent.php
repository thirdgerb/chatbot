<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 用于未定义的intent.
 */
class PlaceHolderIntent extends AbsIntent
{
    /**
     * @var string
     */
    protected $_name;

    public function __construct(string $name, array $entities = [])
    {
        $this->_name = $name;
        parent::__construct($entities);
    }


    public function navigate(Dialog $dialog): ? Navigator
    {
        return null;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->dialog->fulfill();
    }

    public function getDef(): Definition
    {
        $repo = $this->getSession()->intentRepo;
        if ($repo->hasDef($this->_name)) {
            return $repo->getDef($this->_name);
        }
        $def = new PlaceHolderIntentDef($this->_name);
        $repo->registerDef($def);
        return $def;
    }

    public function __sleep(): array
    {
        $fields = parent::__sleep();
        $fields[] = '_name';
        return $fields;
    }

}