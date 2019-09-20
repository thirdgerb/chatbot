<?php


namespace Commune\Chatbot\OOHost\Context\Helpers;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Exiting\ExitingCatcher;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Stages\CallbackStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\StartStageRoute;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Exceptions\NavigatorException;

/**
 * @mixin Definition
 */
trait ContextCaller
{

    public function callExiting(
        int $exiting,
        Context $self,
        Dialog $dialog,
        Context $callback =null
    ): ? Navigator
    {
        $exiting = new ExitingCatcher($exiting, $self, $dialog, $callback);
        $method = Context::EXITING_LISTENER;
        call_user_func([$self, $method], $exiting);
        return $exiting->navigator;
    }

    public function callbackStage(
        Context $self,
        Dialog $dialog,
        string $stage,
        Message $callbackValue = null
    ): Navigator
    {
        $this->checkStageExists($stage);

        $stageRoute = new CallbackStageRoute(
            $stage,
            $self,
            $dialog,
            $callbackValue
        );

        return $this->callStage($stage, $stageRoute);
    }


    public function startStage(
        Context $self,
        Dialog $dialog,
        string $stage,
        bool $dependingCheck = true
    ): Navigator
    {
        $this->checkStageExists($stage);

        // 检查depending entity
        if ($dependingCheck && $stage === Context::INITIAL_STAGE) {
            $entity = $this->dependingEntity($self);
            if (!empty($entity)) {
                return $dialog->goStagePipes(
                    [ $entity->name, $stage ],
                    true
                );
            }
        }

        // 正常启动.
        // 理论上不会有死循环.
        $stageRoute = new StartStageRoute(
            $stage,
            $self,
            $dialog
        );

        return $this->callStage($stage, $stageRoute);
    }



    protected function checkStageExists(string $stage) : void
    {
        if (!$this->hasStage($stage)) {
            throw new ConfigureException(
                'context ' . $this->getName()
                . ' stage ' . $stage
                . ' not found while call it'
            );
        }
    }


    public function callStage(string $stage, Stage $stageRoute) : Navigator
    {
        $caller = $this->getStageCaller($stage);

        try {

            $context = $stageRoute->self;
            $method = Context::STAGE_MIDDLEWARE_METHOD;
            if (method_exists($context, $method)) {
                $context->{$method}($stageRoute);
            }

            $result = call_user_func($caller, $stageRoute);

        } catch (NavigatorException $e) {
            return $e->getNavigator();

        }

        if (!$result instanceof Navigator) {
            throw new LogicException(
                'context ' . $this->getName()
                . ' stage ' . $stage
                . ' should only return value instance of '. Navigator::class
                . ', ' . gettype($result) . ' given'
            );
        }

        return $result;
    }


    abstract protected function getStageCaller(string $stage) : callable ;


}