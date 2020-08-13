<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Parsers;

use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Components\Markdown\Analysers\BranchAnalyser;
use Commune\Components\Markdown\Analysers\ContextAnalyser;
use Commune\Components\Markdown\Mindset\DocRootStageDef;
use Commune\Components\Markdown\Mindset\MDContextDef;
use Commune\Components\Markdown\Mindset\SectionStageDef;
use Commune\Components\Markdown\Options\MDGroupOption;
use Commune\Components\Tree\Prototype\BranchStageDef;
use Commune\Ghost\Context\IContext;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\ArrTree\Branch;
use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Markdown\Parser\MDParser;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMD2ContextParser implements MD2ContextParser
{

    /**
     * @var ProcContainer
     */
    protected $container;

    /**
     * @var array
     */
    protected $contextStub = [
    ];

    /**
     * TreeMd2CtxParser constructor.
     * @param ProcContainer $container
     */
    public function __construct(ProcContainer $container)
    {
        $this->container = $container;
    }


    public function parse(
        MDGroupOption $group,
        MDParser $parser
    ): ContextDef
    {
        $sections = $parser->getSections();

        $tree = $parser->tree;
        $root = $tree->root;
        $rootOrderId = $root->orderId;

        $rootSection = $sections[$rootOrderId];
        unset($sections[$rootOrderId]);

        $markdownId = $parser->doc->id;
        $stageMetas = $this->buildStageMetas(
            $parser,
            $group,
            $markdownId,
            $sections
        );

        $contextDef = $this->makeContextDef(
            $parser,
            $group,
            $markdownId,
            $rootSection,
            $stageMetas
        );

        return $contextDef;
    }

    /**
     * @param MDParser $parser
     * @param MDGroupOption $group
     * @param string $markdownId
     * @param MDSectionData $rootSection
     * @param StageMeta[] $stageMetas
     * @return ContextDef
     */
    protected function makeContextDef(
        MDParser $parser,
        MDGroupOption $group,
        string $markdownId,
        MDSectionData $rootSection,
        array $stageMetas
    ) : ContextDef
    {
        $comments = $rootSection->comments;

        // 取出第一个有标题的区块. 没有的话, 就仍然是根节点.
        reset($stageMetas);
        $first = current($stageMetas);

        // 准备 first stage
        $rootStage = $this->makeRootStage(
            $markdownId,
            $rootSection,
            isset($first) ? $first->stageName : null
        );
        // 插入根节点.
        array_unshift($stageMetas, $rootStage->toMeta());


        $data = [
            'name' => $markdownId,
            'title' => isset($first) ? $first->title : '',
            'desc' => isset($first) ? $first->desc : '',

            'priority' => 1,
            'queryNames' => [],

            // 实际的 stage 是没有标题的环节.
            'rootName' => $parser->tree->root->orderId,
            'tree' => $parser->tree->toNameArr(),
            'stageEvents' => $group->stageEvents,

            // 所有的 branch 都关联到 MDSectionData.
            // 通过 StageMeta, 而不是 tree 来定义的 stage 组件.
            'stages' => $stageMetas,
            'dependingNames' => [],
            'asIntent' => [],
            'memoryScopes' => [],
            'memoryAttrs' => [],
            'strategy' => [],
            'contextWrapper' => IContext::class,
        ];

        $data = $data + $this->contextStub;

        $def =  new MDContextDef($data);
        $map = $group->getAnalyserMapByInterface(
            ContextAnalyser::class,
            true
        );

        foreach ($map as $comment => $handlerName) {

            $contents = $comments[$comment] ?? [];
            if (empty($contents)) {
                continue;
            }

            foreach ($contents as $content) {
                /**
                 * @var ContextAnalyser $handler
                 */
                $handler = $this->container->make($handlerName);
                $def = $handler($content, $def);
                if (!$def instanceof MDContextDef) {
                    return $def;
                }
            }
        }

        return $def;
    }

    protected function makeRootStage(
        string $markdownId,
        MDSectionData $root,
        string $nextStageName = null
    ) : StageDef
    {
        return new DocRootStageDef([
            'name' => ContextUtils::makeFullStageName(
                $markdownId,
                $root->orderId
            ),

            'title' => '',
            'desc' => '',

            'contextName' => $markdownId,
            'stageName' => $root->orderId,
            'asIntent' => null,

            'document' => $root->text,
            'nextStage' => $nextStageName,
        ]);

    }

    /**
     * @param MDParser $parser
     * @param MDGroupOption $group
     * @param string $markdownId
     * @param MDSectionData[] $sections
     * @return StageMeta[]
     */
    protected function buildStageMetas(
        MDParser $parser,
        MDGroupOption $group,
        string $markdownId,
        array $sections
    ) : array
    {
        $defs = [];
        $tree = $parser->tree;
        foreach ($sections as $section) {

            $branch = $tree->branches[$section->orderId];
            $def = $this->makeStageDef(
                $group,
                $branch,
                $section,
                $markdownId
            );

            // 将命名同步回 branch 树.
            $branch->name = $def->getStageShortName();
            $defs[$branch->orderId] = $def;
        }

        // 同步树形配置
        $metas = [];
        $firstGeneration = array_map(function(Branch $firstBorn) {
            return $firstBorn->orderId;
        }, $tree->root->children);

        foreach ($defs as $orderId => $def) {
            // 等 name 变更后, 重新同步树形结构.
            if ($def instanceof SectionStageDef) {

                $branch = $tree->branches[$orderId];
                $orderId = $branch->orderId;

                $def->orderId = $branch->orderId;
                $def->parent = in_array($orderId, $firstGeneration)
                    ? null
                    : $this->getBranchName($branch->parent);

                $def->elder = $this->getBranchName($branch->elder);
                $def->younger = $this->getBranchName($branch->younger);
                $def->children = array_map(function(Branch $branch) {
                    return $this->getBranchName($branch);
                }, $branch->children);
                $def->depth = $branch->depth;
            }
            $metas[$orderId] = $def->toMeta();
        }

        return $metas;
    }


    protected function getBranchName(? Branch $branch) : ? string
    {
        return empty($branch)
            ? null
            : $branch->name;
    }

    /**
     * @param MDGroupOption $group
     * @param Branch $branch
     * @param MDSectionData $data
     * @param string $markdownId
     * @return StageDef|BranchStageDef
     */
    protected function  makeStageDef(
        MDGroupOption $group,
        Branch $branch,
        MDSectionData $data,
        string $markdownId
    ) : StageDef
    {
        $stub = [
            'name' => ContextUtils::makeFullStageName(
                $markdownId,
                $branch->orderId
            ),
            'title' => $data->title,
            'desc' => $data->title,
            'contextName' => $markdownId,
            'stageName' => $branch->orderId,
            'groupName' => $group->groupName,
            'events' => $group->stageEvents,
            'asIntent' => [],
            'ifRedirect' => null,
        ];

        $def = new SectionStageDef($stub);

        $comments = $data->comments;
        if (empty($comments)) {
            return $def;
        }

        $analyserMap = $group->getAnalyserMapByInterface(BranchAnalyser::class, true);

        foreach ($analyserMap as $comment => $analyserName) {
            $contents = $comments[$comment] ?? [];
            if (empty($contents)) {
                continue;
            }

            /**
             * @var BranchAnalyser $analyser
             */
            $analyser = $this->container->make($analyserName);

            foreach ($contents as $content) {
                $def = $analyser($content, $def);
                // 如果 def 被替换了就直接返回.
                if (!$def instanceof BranchStageDef) {
                    return $def;
                }
            }
        }

        return $def;
    }

    protected function genComment(array $comments) : \Generator
    {
        foreach ($comments as $comment) {
            $separators = StringUtils::separateAnnotation($comment);
            if (empty($separators)) {
                continue;
            }

            yield $separators;
        }
    }


}