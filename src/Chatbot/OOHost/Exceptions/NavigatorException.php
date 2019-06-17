<?php


namespace Commune\Chatbot\OOHost\Exceptions;


use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\OOHost\Directing\Navigator;

class NavigatorException extends LogicException
{

    /**
     * @var Navigator
     */
    protected $navigator;

    /**
     * NavigatorException constructor.
     * @param Navigator $navigator
     */
    public function __construct(Navigator $navigator)
    {
        $this->navigator = $navigator;
        parent::__construct();
    }

    /**
     * @return Navigator
     */
    public function getNavigator(): Navigator
    {
        return $this->navigator;
    }

}