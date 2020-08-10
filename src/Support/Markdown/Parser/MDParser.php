<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Markdown\Parser;

use Commune\Support\ArrTree\Branch;
use Commune\Support\ArrTree\Tree;
use Commune\Support\Markdown\Data\MDDocumentData;
use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Markdown\Exceptions\InvalidTreeMarkdownException;


/**
 * Markdown 文档结构化
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property Tree $tree
 * @property MDDocumentData $doc
 *
 * @property Branch $currentBranch
 * @property MDSectionData $currentSection
 *
 * @property MDSectionData[] $sections
 *
 * @property string[] $lines
 * @property int[] $lineModes
 * @property int $index
 *
 * @property-read string[] $archiveComments
 *
 */
interface MDParser
{
    const FUNC_PARSE = 'parse';

    // 我们的需求只要有以下几种就可以了.
    const LINE_EMPTY = 0;
    const LINE_STRING = 1;
    const LINE_TITLE = 1 << 1 | self::LINE_STRING;
    const LINE_COMMENT = 1 << 5 | self::LINE_STRING;


    public static function parse(
        string $id,
        string $rootName,
        string $content,
        array $archiveComments = []
    ) : MDParser;


    public function makeSection(
        Branch $branch,
        string $title,
        int $level
    ) : MDSectionData;

    public function switchBranch(Branch $branch) : void;

    /**
     * @param string $error
     * @throws InvalidTreeMarkdownException
     */
    public function error(string $error) : void;

    public function appendText(string $line) : int;

    public function getLineMode(int $index) : int;

    public function toMarkdown() : string;

    public function getDocument() : MDDocumentData;

    /**
     * @return MDSectionData[]
     */
    public function getSections() : array;

}