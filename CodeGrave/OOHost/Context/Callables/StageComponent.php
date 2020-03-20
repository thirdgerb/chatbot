<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 对话阶段可以共享的组件.
 */
interface StageComponent
{

    public function __invoke(Stage $stage) : Navigator;
}