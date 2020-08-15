<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Markdown\Data;

use Commune\Support\Markdown\MarkdownUtils;
use Commune\Support\Option\AbsOption;
use Commune\Support\Utils\StringUtils;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id                文档块的唯一 ID
 * @property-read string $orderId          文档的使用数字目录标记的文档位置. 例如 root_0_1_2_3_1
 *
 * @property-read int $order               文档块在父级 section 中的序号.
 * @property-read int $depth               在树状结构中的深度
 *
 * @property string $title                 标题的内容.
 * @property int $level                    标题的级别. h1 h2 h3...
 *
 * @property string[] $texts                  文本块内容.
 * @property string[][] $comments               注释内容.
 *
 */
class MDSectionData extends AbsOption
{
    const IDENTITY = 'id';

    const BLOCK_SEPARATOR = 'break';

    protected $_text;

    public static function stub(): array
    {
        return [
            'id' => '',
            'orderId' => '',
            'order' => 0,
            'title' => '',
            'level' => 0,
            'texts' => [],
            'comments' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }


    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['orderId'])
            ?? parent::validate($data);
    }

    public function appendLine(string $line) : void
    {
        $texts = $this->_data['texts'] ?? [];
        $text = array_pop($texts);
        if (is_null($text)) {
            $text = $line;
        } else {
            $text .= PHP_EOL . $line;
        }
        array_push($texts, $text);
        $this->_data['texts'] = $texts;
    }

    public function appendComment(string $comment, string $content) : void
    {
        $contents = $this->_data['comments'][$comment] ?? [];
        $contents[] = $content;
        $this->_data['comments'][$comment] = $contents;
    }

    public function appendBlock() : void
    {
        $this->_data['texts'][] = '';
    }

    public function toText() : string
    {
        if (isset($this->_text)) {
            return $this->_text;
        }

        $level = $this->level;

        // 完成 title
        $title = '';
        if (!empty($level) && !StringUtils::isEmptyStr($this->title)) {
            for($i =0; $i < $level; $i ++) {
                $title.= "#";
            }
            $title.= ' ' . $this->title;
        }

        // 文本块.
        $blockText = '';
        $blocks = $this->texts;
        if (!empty($blocks)) {
            $separator = MarkdownUtils::createCommentLine(self::BLOCK_SEPARATOR);
            $blockText = implode(
                PHP_EOL . $separator . PHP_EOL ,
                array_map('trim', $blocks)
            );
        }

        // 注释块
        $comments = $this->comments;
        $commentBlock = '';
        if (!empty($comments)) {
            $commentTypes = [];
            foreach ($comments as $type => $contents) {

                $commentContents = [];
                foreach ($contents as $content) {
                    $commentLine = MarkdownUtils::createCommentLine($type, $content);
                    $commentContents[] = $commentLine;
                }
                $commentTypes[] = implode(PHP_EOL, $commentContents);
            }
            $commentBlock = implode(PHP_EOL, $commentTypes);
        }

        // 保证大块之间上下都无空行.
        $sections = [];
        if (!StringUtils::isEmptyStr($title)) {
            $sections[] = trim($title);
        }
        if (!StringUtils::isEmptyStr($blockText)) {
            $sections[] = trim($blockText);
        }
        if (!StringUtils::isEmptyStr($commentBlock)) {
            $sections[] = trim($commentBlock);
        }

        $text = implode(PHP_EOL . PHP_EOL , $sections);
        return $this->_text = trim($text);
    }

}