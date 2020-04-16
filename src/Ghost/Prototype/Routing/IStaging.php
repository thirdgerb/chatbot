<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Routing;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Staging;
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Ghost\Prototype\Operators\Events\ActivateStage;
use Commune\Ghost\Prototype\Operators\Staging\NextStages;
use Commune\Ghost\Prototype\Operators\Staging\ResetContext;
use Commune\Ghost\Prototype\Operators\Staging\RestartContext;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStaging implements Staging
{
    /**
     * @var Stage
     */
    protected $stage;

    /**
     * IStaging constructor.
     * @param Stage $stage
     */
    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
    }

    public function restartContext(): Operator
    {
        return new RestartContext($this->stage->node);
    }

    public function resetContext(): Operator
    {
        return new ResetContext($this->stage->self, $this->stage->node);
    }

    public function next(...$stageNames): Operator
    {
        return new NextStages($this->stage->def, $stageNames, false);
    }

    public function swerve(...$stageNames): Operator
    {
        return new NextStages($this->stage->def, $stageNames, true);
    }


}