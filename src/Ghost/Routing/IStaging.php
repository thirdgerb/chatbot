<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Routing;

use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routing\Staging;
use Commune\Blueprint\Ghost\Stage\Stage;
use Commune\Ghost\Operators\Events\ToActivateStage;
use Commune\Ghost\Operators\Staging\NextStages;
use Commune\Ghost\Operators\Staging\ResetContext;
use Commune\Ghost\Operators\Staging\RestartContext;


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