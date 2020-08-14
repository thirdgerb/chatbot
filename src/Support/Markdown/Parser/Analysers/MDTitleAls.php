<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Markdown\Parser\Analysers;

use Commune\Support\Markdown\MarkdownUtils;
use Commune\Support\Markdown\Parser\MDAnalyser;
use Commune\Support\Markdown\Parser\MDParser;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MDTitleAls extends MDAnalyser
{
    public function __invoke(int $index, string $line): ? int
    {
        $titleInfo = MarkdownUtils::parseTitle($line);
        if (!empty($titleInfo)) {
            list($level, $title) = $titleInfo;
            return $this->addTitle($level, $title);
        }

        return null;
//        // 更多情况暂时先不考虑. 还有列表等各种冲突情况. 未来还是要有一个严谨的 markdown 解析器为前提.
//        $level = MarkdownUtils::maybeTitleUnderline($line);
//
//        if ($level <= 0) {
//            return null;
//        }
//
//        $lastLine = $this->parser->lines[$index - 1] ?? '';
//        $lastLine = trim($lastLine);
//        // 上一行不能为空.
//        if (StringUtils::isEmptyStr($lastLine)) {
//            return null;
//        }
//
//        $mode = $this->parser->lineModes[$index - 2] ?? MDParser::LINE_EMPTY;
//        // 上两行必须为空.
//        if ($mode !== MDParser::LINE_EMPTY) {
//            return null;
//        }
//
//        $title = $lastLine;
//        // 变更类型.
//        $this->parser->lineModes[$index - 1] = MDParser::LINE_TITLE;
//        return $this->addTitle($level, $title);
    }


    /**
     * @param int $level
     * @param string $title
     * @return int
     */
    public function addTitle(int $level, string $title) : int
    {
        if ($level <= 0) {
            $this->parser->error("title level must above 0, $level given");
        }

        $parser = $this->parser;

        // 子女. 越级也不管. 血缘就是这么乱!
        if ($level > $parser->currentSection->level) {
            $branch = $parser->currentBranch->father('');
            $parser->makeSection($branch, $title, $level);
            $parser->switchBranch($branch);
            return MDParser::LINE_TITLE;

            // 弟妹
            // 递归找爹爹爹去处理.
        } else {
            $parser->switchBranch($parser->currentBranch->parent);
            return $this->addTitle($level, $title);
        }
    }


}