<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Routing;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Staging;
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Ghost\Prototype\Operators\Staging\ToFulfillStage;
use Commune\Ghost\Prototype\Operators\Staging\NextStage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStaging implements Staging
{

    /**
     * @var Stage
     */
    protected $stage;

    public function restartContext(): Operator
    {
    }

    public function resetContext(): Operator
    {
        // TODO: Implement resetContext() method.
    }

    public function next(...$stageNames): Operator
    {
        return new NextStage($this->stage->def, $stageNames);
    }

    public function fulfill(): Operator
    {
        return new ToFulfillStage($this->stage->def, $this->stage->self);
    }


}