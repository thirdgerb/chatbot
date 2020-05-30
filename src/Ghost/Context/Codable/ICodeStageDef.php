<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Builders\IStageBuilder;
use Commune\Ghost\Stage\AbsStageDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $contextName
 * @property-read string $stageName
 *
 * @property-read string $title
 * @property-read string $desc
 * @property-read IntentMeta $asIntent
 *
 */
class ICodeStageDef extends AbsStageDef
{



    /*------ callable ------*/

    public function onActivate(Activate $dialog): Operator
    {
        $builder = $this->getStageBuilder($dialog->context);
        return $builder->fire($dialog) ?? $dialog->await();
    }

    public function onReceive(Receive $dialog): Operator
    {
        $builder = $this->getStageBuilder($dialog->context);
        return $builder->fire($dialog) ?? $dialog->confuse();
    }

    public function onRedirect(Dialog $prev, Dialog $current): ? Operator
    {
        $builder = $this->getStageBuilder($current->context);
        return $builder->fireRedirect($prev, $current);
    }

    public function onResume(Resume $dialog): ? Operator
    {
        $builder = $this->getStageBuilder($dialog->context);
        return $builder->fire($dialog);
    }

    protected function getMethodName() : string
    {
        return CodeContext::STAGE_BUILDER_PREFIX . $this->getStageShortName();
    }

    protected function getStageBuilder(Context $context) : IStageBuilder
    {
        $builder = new IStageBuilder();
        $creator = [$context, $this->getMethodName()];
        return $creator($builder);
    }


    /*------ wrapper ------*/

    /**
     * @return StageMeta
     */
    public function getMeta(): Meta
    {
        return new StageMeta([
            'name' => $this->name,
            'contextName' => $this->contextName,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => static::class,
            'config' => [
                'asIntent' => $this->asIntent
            ],
        ]);
    }

    /**
     * @param StageMeta $meta
     * @return Wrapper
     */
    public static function wrap(Meta $meta): Wrapper
    {
        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;

        return new static($config);
    }


}