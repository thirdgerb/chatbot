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

use Commune\Support\Option\AbsOption;
use Commune\Support\Struct\AStruct;
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
 * @property string $name                  文档块的 ID.
 * @property string $title                 标题的内容.
 * @property int $level                    标题的级别. h1 h2 h3...
 * @property string $text                  文本块内容.
 * @property string[][] $comments               注释内容.
 *
 */
class MDSectionData extends AbsOption
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'orderId' => '',
            'name' => '',
            'order' => 0,
            'title' => '',
            'level' => 0,
            'text' => '',
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
        $this->_data['text'] = $this->_data['text'] . PHP_EOL . $line;
    }

    public function appendComment(string $comment, string $content) : void
    {
        $contents = $this->_data['comments'][$comment] ?? [];
        $contents[] = $content;
        $this->_data['comments'][$comment] = $contents;
    }

    public function toText() : string
    {
        $level = $this->level;
        $text = PHP_EOL;
        for($i =0; $i < $level; $i ++) {
            $text.= "#";
        }

        $text.= ' ' . $this->title;

        $text .= PHP_EOL . PHP_EOL . trim($this->text);
        $comments = $this->comments;
        if (empty($comments)) {
            return $text;
        }

        // 分割.
        $text .= PHP_EOL;
        foreach ($comments as $type => $contents) {
            foreach ($contents as $content) {
                $text .=  (PHP_EOL . "[$type]: $content");
            }
        }

        // 保证大块之间上下都无空行.
        return $text;
    }

//    public function __destruct()
//    {
//        unset(
//            $this->_commentMap,
//            $this->_content
//        );
//        parent::__destruct();
//    }

}