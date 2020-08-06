<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Tree\Prototype;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\Traits\ContextDefTrait;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\ArrTree\Branch;
use Commune\Support\ArrTree\Tree;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read int $priority
 *
 *
 * @property-read array $tree
 * @property-read bool $appendingBranch
 * @property-read string[] $events
 * @property-read string|null $relativeOption
 *
 *
 *
 * @property-read string[] $auth
 * @property-read array $dependingNames
 *
 * @property-read string[] $memoryScopes
 * @property-read array $memoryAttrs
 *
 * @property-read IntentMeta|null $asIntent
 * @property-read null|array $comprehendPipes
 *
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 *
 * @property-read string $contextWrapper
 */
class TreeContextDef extends AbsOption implements ContextDef
{
    const IDENTITY = 'name';

    const FIRST_STAGE = 'root';
    const CANCEL_STAGE = 'cancel';

    use ContextDefTrait;

    /**
     * @var array|null
     */
    protected $_stageMap;

    public static function stub(): array
    {
        return [


            /*----- 核心参数 -----*/

            // context 的全名. 同时也是意图名称.
            'name' => '',
            // context 的标题. 可以用于 精确意图校验.
            'title' => '',
            // context 的简介. 通常用于 askChoose 的选项.
            'desc' => '',
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 0,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [],

            'tree' => [],
            'events' => [],
            'appendingBranch' => false,
            'relativeOption' => null,




            /*---- 以下为可选参数 ----*/


            // auth, 访问时用户必须拥有的权限. 用类名表示.
            'auth' => [],

            // Context 启动时, 会依次检查的参数. 当这些参数都不是 null 时, 认为 Context::isPrepared
            'dependingNames' => [],

            'asIntent' => null,

            // 定义 context 上下文记忆的作用域.
            // 相关作用域参数, 会自动添加到 query 参数中.
            // 作用域为空, 则是一个 session 级别的短程记忆.
            // 不为空, 则是长程记忆, 会持久化保存.
            'memoryScopes' => null,
            // memory 记忆体的默认值.
            'memoryAttrs' => [],

            'comprehendPipes' => null,

            'stageRoutes' => ['*'],
            'contextRoutes' => ['*'],

            // context 实例的封装类.
            'contextWrapper' => '',
        ];
    }

    public static function relations(): array
    {
        return [
            'asIntent' => IntentMeta::class,
        ];
    }

    /*--------  stage builder --------*/

    public function firstStage(): ? string
    {
        return static::FIRST_STAGE;
    }

    public function eachPredefinedStage(): \Generator
    {
        $map = $this->getPredefinedStageMap();
        foreach ($map as $stage) {
            yield $stage;
        }
    }

    public function getPredefinedStageMap() : array
    {
        if (isset($this->_stageMap)) {
            return $this->_stageMap;
        }

        $data = $this->tree;
        $tree = new Tree();
        $append = $this->appendingBranch ? '.' : '';

        $tree->build($data, static::FIRST_STAGE, $append);
        new Branch($tree, static::CANCEL_STAGE);

        $this->_stageMap = [];

        foreach ($tree->branches as $branch) {
           $stage = $this->buildStage($branch);
           $this->_stageMap[$stage->getStageShortName()] = $stage;
        }

        return $this->_stageMap;
    }

    protected function getFullStageName(?Branch $branch) : ? string
    {
        if (!isset($branch)) {
            return null;
        }
        return ContextUtils::makeFullStageName(
            $this->name,
            $branch->name
        );
    }

    protected function buildStage(Branch $branch) : BranchStageDef
    {
        $fullName = $this->getFullStageName($branch);
        $children = array_map(function(Branch $branch) {
            return $this->getFullStageName($branch);
        }, $branch->children);

        $stage = new BranchStageDef([
            'name' => $fullName,
            'title' => $fullName,
            'desc' => $fullName,
            'contextName' => $this->name,
            'stageName' => $branch->name,

            // 爹妈
            'parent' => $this->getFullStageName($branch->parent),
            // 儿女
            'children' => $children,
            // 哥哥姐姐
            'elder' => $this->getFullStageName($branch->elder),
            // 弟弟妹妹
            'younger' => $this->getFullStageName($branch->younger),
            'events' => $this->events,
            'asIntent' => null,
            'ifRedirect' => null,
        ]);
        return $stage;
    }

    public function getPredefinedStage(string $name): ? StageDef
    {
        return $this->getPredefinedStageMap()[$name] ?? null;
    }


    /*-------- 更多默认属性 --------*/

    public function getDependingNames(): array
    {
        return $this->dependingNames;
    }

    public function comprehendPipes(Dialog $current): ? array
    {
        return $this->comprehendPipes;
    }

    /*-------- exit --------*/

    public function onCancelStage(): ? string
    {
        return static::CANCEL_STAGE;
    }

    public function onQuitStage(): ? string
    {
        return null;
    }



    /*-------- asStage --------*/

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return null;
    }

    /*-------- common routes --------*/

    public function commonStageRoutes(): array
    {
        return $this->stageRoutes;
    }

    public function commonContextRoutes(): array
    {
        return $this->contextRoutes;
    }

    public function __destruct()
    {
        unset($this->_stageMap);
        parent::__destruct();
    }

}