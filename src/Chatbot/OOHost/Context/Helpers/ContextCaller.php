<?php


namespace Commune\Chatbot\OOHost\Context\Helpers;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Exiting\ExitingCatcher;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Stages\CallbackStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\ExitingStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\FallbackStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\IntendedStageRoute;
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

        $stage = $dialog->history->currentTask()->getStage();
        $this->checkStageExists($stage);

        $stageRoute = new ExitingStageRoute($stage, $exiting);
        $caller = $this->getStageCaller($stage);

        try {

            // 先检查 Stage 是否存在退出逻辑.
            call_user_func($caller, $stageRoute);
            $navigator = $exiting->navigator;
            if (isset($navigator)) {
                return $navigator;
            }

            // 如果 Stage 自带退出逻辑没有生效, 则检查 Context 是否定义了通用的退出逻辑.
            $method = Context::EXITING_LISTENER;
            if (method_exists($self, $method)) {
                call_user_func([$self, $method], $exiting);
            }

            // 无论如何, 都只返回 Exiting 自己的 Navigator, 允许为 null.
            return $exiting->navigator;

        } catch (NavigatorException $e) {

            return $e->getNavigator();
        }

    }

    protected function checkStageExists(string $stage) : void
    {
        if (!$this->hasStage($stage)) {
            throw new ChatbotLogicException(
                'context ' . $this->getName()
                . ' stage ' . $stage
                . ' not found while call it'
            );
        }
    }

    public function callbackStage(
        Context $self,
        Dialog $dialog,
        string $stage,
        Message $userMessage
    ): Navigator
    {
        $this->checkStageExists($stage);

        $stageRoute = new CallbackStageRoute(
            $stage,
            $self,
            $dialog,
            $userMessage
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


    /**
     * depend 回调 stage
     * @param Context $self
     * @param Dialog $dialog
     * @param string $stage
     * @param Context $callbackContext
     * @return mixed
     */
    public function intendToStage(
        Context $self,
        Dialog $dialog,
        string $stage,
        Context $callbackContext
    ) : Navigator
    {
        $this->checkStageExists($stage);

        $stageRoute = new IntendedStageRoute(
            $stage,
            $self,
            $dialog,
            $callbackContext
        );

        return $this->callStage($stage, $stageRoute);

    }

    /**
     * sleep 的 context 被重新唤醒.
     * @param Context $self
     * @param Dialog $dialog
     * @param string $stage
     * @return Navigator
     */
    public function fallbackStage(
        Context $self,
        Dialog $dialog,
        string $stage
    ) : Navigator
    {
        $this->checkStageExists($stage);

        $stageRoute = new FallbackStageRoute(
            $stage,
            $self,
            $dialog
        );

        return $this->callStage($stage, $stageRoute);

    }

    public function callStage(string $stage, Stage $stageRoute) : Navigator
    {
        $caller = $this->getStageCaller($stage);

        try {

            $context = $stageRoute->self;
            $method = Context::STAGE_COMMON_BUILDER;
            if (method_exists($context, $method)) {
                $context->{$method}($stageRoute);
            }

            $result = call_user_func($caller, $stageRoute);

        } catch (NavigatorException $e) {
            return $e->getNavigator();

        }

        if (!$result instanceof Navigator) {
            throw new ChatbotLogicException(
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