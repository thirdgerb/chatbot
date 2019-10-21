<?php


namespace Commune\Components\SimpleWiki\Options;


use Commune\Support\Option;
use Commune\Support\Utils\StringUtils;

/**
 * 简单维基的配置.
 *
 * @property-read string $intentName 意图名称. 根据路径生成出来. 路径构成如下:
 *
 * sw.groupName.path1.path2.path3
 * sw 是模块公共的前缀.
 * 后面的部分, 与 yaml 文件存储路径相同. 第一个文件夹是分组名, 对应 GroupOption
 *
 * 例如
 * - 根目录: resources/wiki/
 * - 文件路径: resources/wiki/demo/conversation/whatIsConversation.yml
 * - 意图名: sw.demo.conversation.whatIsConversation
 * - 分组名: demo
 *
 *
 * @property-read string[] $examples 意图的语料. 与 IntentCorpusOption 配置一致.
 * @property-read string $description 意图的描述.
 * @property-read string[] $suggestions 猜你想问的关联内容. 有以下允许的格式:
 *
 * - [intentName] : 完整的意图名称.
 * - [intentAlias] : 在 group 内定义的 alias
 * - [.intentName] : 在同级目录下的意图.
 * - [..intentName] : 上级目录下的意图.
 * - [...intentName] : 上两级目录下的意图... 依此类推.
 * - [/intentName] : 同 group 下的意图名称.
 * - [intentName.*] : 用.* 结尾, 表示这个目录下所有的意图.
 *
 *
 * @property-read string[] $replies 默认回复.
 *
 * 回复的数组, 每一个元素是一批独立的消息, 发送完后会调用 askContinue 询问用户是否继续.
 * 而每一个元素可以用 "|" 隔开, 表示多个独立的消息.
 * 而消息的 replyId, 会用 GroupOption::messagePrefix 作为前缀.
 *
 *
 * 例如:
 *  replies :
 *      -   intro1|intro2
 *      -   intro3|intro4
 *
 * 等于第一轮回复  say()->info($msgPrefix . $intro1)->info($msgPrefix . $intro2)
 * 第二轮回复  say()->info($msgPrefix . $intro3)->info($msgPrefix . $intro4)
 *
 * 如果只想回复纯文本, 不做任何转义, 则以 "~" 开头就可以.
 *
 */
class WikiOption extends Option
{
    const IDENTITY = 'intentName';

    const INTENT_NAME_PREFIX = 'sw';

    /**
     * @var string[]
     */
    protected $sections;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $groupName;

    public static function stub(): array
    {
        return [
            'intentName' => '',
            'description' => '',
            'examples' => [
            ],
            'suggestions' => [
            ],
            'replies' => [
            ],
        ];
    }

    protected function init(array $data): array
    {
        $data['intentName'] = StringUtils::normalizeContextName($data['intentName']);
        return parent::init($data);
    }

    public static function validate(array $data): ? string
    {
        if (empty($data['intentName'])) {
            return 'intentName is required';
        }

        if (!is_string($data['intentName'])) {
            return 'intentName must be string';
        }

        if (explode('.', $data['intentName']) < 3) {
            return 'invalid intentName, at list sw.groupName.baseName 3 sections';
        }

        if (empty($data['replies'])) {
            return 'replies are required';
        }

        return null;
    }

    public function getNameSections() : array
    {
        return $this->sections
            ?? $this->sections = explode('.', $this->intentName);
    }

    public function getBaseName() : string
    {
        $sections = $this->getNameSections();
        return end($sections);
    }

    public function getPrefix() : string
    {
        if (isset($this->prefix)) {
            return $this->prefix;
        }

        $sections = $this->getNameSections();
        array_pop($sections);

        return $this->prefix = implode('.', $sections);
    }

    public function getGroupName() : string
    {
        if (isset($this->groupName)) {
            return $this->groupName;
        }

        $sections = $this->getNameSections();
        return $this->groupName = $sections[1];
    }
    
    public function getPathSections() : array
    {
        $sections = $this->getNameSections();
        // 去掉一个本名.
        array_pop($sections);
        // 去掉前缀. 
        array_shift($sections);
        // 去掉组名
        array_shift($sections);
        return $sections;
    }


}