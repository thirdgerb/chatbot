<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Tree\Impl\SimpleText;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Tree\Strategies\ATreeActivateStrategy;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class STStrategy extends ATreeActivateStrategy
{
    public function onActivate(Dialog\Activate $dialog): Operator
    {
        $def = $this->getBranchStageDef($dialog);
        $editor = STManagerContext::genUcl(['stageName'=> $def->getName()]);

        if (!$def->isComplete()) {
            $context = $editor->findContext($dialog->cloner);
            $created = $context->getTask()->isStatus(Context::CREATED);
            if ($created) {
                return $dialog
                    ->send()
                    ->notice("当前节点会话信息不完整, 进入编辑")
                    ->over()
                    ->blockTo($editor);
            }
        }

        $choices = [];
        $current = $dialog->ucl;
        $parent = $def->parent;
        $children = $def->children;
        if (!empty($parent)) {
            $choices[] = $current->goStageByFullname($parent);
        }

        if (!empty($children)) {
            array_push(
                $choices,
                ...array_map([$current, 'goStageByFullname'], $children)
            );
        }

        return $dialog
            ->send()
            ->info($def->getName())
            ->over()
            ->await(
                [],
                [$editor]
            )
            ->askChoose(
                '请选择:',
                $choices
            );
    }

    public function onReceive(Dialog\Receive $dialog): Operator
    {
        return $dialog->confuse();
    }

    public function onResume(Dialog\Resume $dialog): ? Operator
    {
        return null;
    }


}