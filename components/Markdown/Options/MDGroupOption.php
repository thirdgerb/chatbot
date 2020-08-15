<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Options;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Components\Markdown\Mindset\MDContextDef;
use Commune\Support\Option\AbsOption;
use Commune\Components\Markdown\Analysers;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Components\Markdown\Parsers\IMD2ContextParser;
use Commune\Components\Markdown\DefStrategy\SectionStrategy;
use Commune\Support\Markdown\Parser\IMDParser;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $groupName
 * @property-read string $relativePath          文件夹相对路径.
 * @property-read string $namespace
 *
 *
 * @property-read array $contextStub            context 的默认配置.
 * @see MDContextDef
 *
 * @property-read string[] $stageEvents         stageEvent => handler Map
 *
 * @property-read string $markdownParser        markdown parser name
 * @property-read string $contextParser         markdown option to context def
 *
 * @property-read string[] $staticAnalysers
 * @property-read string[] $dynamicAnalysers
 */
class MDGroupOption extends AbsOption
{
    const IDENTITY = 'groupName';

    protected $_analyserMap = [];

    public static function stub(): array
    {
        return [
            'groupName' => '',
            'relativePath' => '',
            // 命名空间 + 文件的相对路径 = document id
            'namespace' => '',
            // 根节点的名称. 用于 contextDef 的定义, 但不作为 stage
            'rootName' => 'root',

            // 公共的 stage 事件.
            'stageEvents' => [
                Dialog::ANY => SectionStrategy::class,
            ],


            'contextStub' => [
                'priority' => 1,
                'strategy' => [
                    'stageRoutes' => ['*'],
                    'contextRoutes' => ['*'],
                ],
            ],

            // markdown 默认的分析器.
            'markdownParser' =>  IMDParser::class,
            // 将 option 变成 ContextDef 的工具.
            'contextParser' => IMD2ContextParser::class,

            // 静态分析工具
            'staticAnalysers' => [

                // branch
                'stageName' => Analysers\Stage\StageNameAls::class,
                'stageTitle' => Analysers\Stage\StageTitleAls::class,
                'stageDesc' => Analysers\Stage\StageDescAls::class,
                'stageEvent' => Analysers\Stage\StageEventAls::class,
                'intentExample' => Analysers\Stage\IntentExampleAls::class,
                'intentSpell' => Analysers\Stage\IntentSpellAls::class,

                // context
                // 'context.name'
                // 'context.title'
                // 'context.desc'

                // await
            ],

            // 动态分析工具.
            'dynamicAnalysers' => [
                'info' => Analysers\Message\InfoAls::class,
                'error' => Analysers\Message\ErrorAls::class,
                'warning' => Analysers\Message\WarningAls::class,

            ],

        ];
    }

    public static function relations(): array
    {
        return [
        ];
    }

    /**
     * 获取所有静态分析工具使用的注解.
     * @return string[]
     */
    public function getStaticComments() : array
    {
        $comments = [];
        foreach ($this->staticAnalysers as $index => $analyserName) {
            $comments[] = is_string($index)
                ? $index
                : $this->getCommentOfAnalyser($analyserName);
        }

        return array_unique($comments);
    }

    protected function getCommentOfAnalyser(string $analyserName) : string
    {
        return call_user_func([$analyserName, AnalyserInterface::FUNC_ID]);
    }

    public function getAnalyserMapByInterface(
        string $interface,
        bool $isStatic
    ) : array
    {
        $defines = $isStatic ? $this->staticAnalysers : $this->dynamicAnalysers;

        // 值不重要.
        $i = $isStatic ? 'a' : 'b';
        if (isset($this->_analyserMap[$i][$interface])) {
            return $this->_analyserMap[$i][$interface];
        }

        $map = [];

        foreach ($defines as $index => $analyserName) {
            if (is_a($analyserName, $interface, true)) {

                $comment = is_string($index)
                    ? $index
                    : $this->getCommentOfAnalyser($analyserName);

                // 现阶段每个 comment 只能有一个 handler
                if (array_key_exists($comment, $map)) {
                    $groupName = $this->groupName;
                    throw new CommuneLogicException(
                        "markdown group option of $groupName defines duplicated analyser ($analyserName) for comment $comment, implement $interface."

                    );
                }

                $map[$comment] = $analyserName;
            }
        }

        return $this->_analyserMap[$i][$interface] = $map;
    }

}