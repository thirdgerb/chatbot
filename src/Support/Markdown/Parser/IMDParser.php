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
use Commune\Support\Markdown\Parser\Analysers;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMDParser implements MDParser
{
    /*----- config -----*/

    protected $analysers = [
        Analysers\TitleAls::class,
        Analysers\CommentAls::class,
    ];
    

    /*----- cached -----*/

    /**
     * @var MDDocumentData
     */
    public $doc;

    /**
     * @var MDSectionData[]
     */
    public $sections = [];

    /**
     * @var Tree
     */
    public $tree;

    /**
     * @var string[]
     */
    public $lines;

    /**
     * @var MDSectionData
     */
    public $currentSection;

    /**
     * @var Branch
     */
    public $currentBranch;

    /**
     * @var int
     */
    public $index = -1;
    
    public $lineModes = [];

    /**
     * @var MDAnalyser[]
     */
    protected $pipelines = [];

    /**
     * IMDParser constructor.
     * @param MDDocumentData $doc
     * @param Tree $tree
     * @param string[] $lines
     */
    public function __construct(MDDocumentData $doc, Tree $tree, array $lines)
    {
        $this->doc = $doc;
        $this->tree = $tree;
        $this->lines = $lines;
        $this->currentBranch = $tree->root;
        $this->currentSection = $this->makeSection(
            $this->currentBranch,
            '',
            0
        );

        $this->initAnalysers();
    }

    protected function initAnalysers() : void
    {
        foreach ($this->analysers as $abstract) {
            $this->pipelines[] = new $abstract($this);
        }
    }


    public static function parse(
        string $id,
        string $rootName,
        string $content
    ): MDParser
    {
        $lines = explode(PHP_EOL, $content);
        $doc = new MDDocumentData([
            'id' => $id,
            // 根节点名称.
            'rootName' => $rootName,
            // 用序号呈现的树.
            // 通过 orderId 来获取所有的子节点.
            'tree' => [],
        ]);

        $tree = Tree::build([], $rootName, '_');

        $self = new static($doc, $tree, $lines);

        $linesCount = count($lines);
        for ($i = 0; $i < $linesCount; $i ++ ) {
            $self = $self->readLine($i);
        }

        // 完成树形结构赋值.
        $self->doc->tree = $tree->toOrderArr()[$rootName];
        return $self;
    }

    public function readLine(int $index) : MDParser
    {
        // 获取 line
        if ($index <= $this->index) {
            $this->error("can not backward read line at $index");
        }

        $this->index = $index;
        $line = $this->lines[$index] ?? null;
        if (is_null($line)) {
            return $this;
        }

        return $this->controlUnit($index, $line);
    }

    public  function controlUnit(int $index, string $line) : MDParser
    {
        $parser = null;
        foreach ($this->pipelines as $pipe) {
            $mode = $pipe($index, $line);
            if (isset($mode)) {
                $this->lineModes[$index] = $mode;
                return $this;
            }
        }
        $this->lineModes[$index] = $this->appendText($line);
        return $this;
    }
    
    
    public function appendText(string $line) : int
    {
        $this->currentSection->appendLine($line);

        $real = trim($line);
        return StringUtils::isEmptyStr($real)
            ? MDParser::LINE_EMPTY
            : MDParser::LINE_STRING;
    }

    public function makeSection(
        Branch $branch,
        string $title,
        int $level
    ) : MDSectionData
    {
        $section = new MDSectionData([
            'id' => $this->doc->id . "." . $branch->orderId,
            'orderId' => $branch->orderId,
            'order' => $branch->order,
            'title' => $title,
            'level' => $level,
            'text' => '',
            'comments' => [],
        ]);

        $this->sections[$section->orderId] = $section;
        return $section;
    }

    public function getDocument(): MDDocumentData
    {
        return $this->doc;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function toMarkdown(): string
    {
        // 生成 markdown 文档.
        $texts = array_map(function(MDSectionData $data) {
            return $data->toText();
        }, $this->sections);

        $text = implode(PHP_EOL . PHP_EOL, $texts);

        //再 trim 掉多余的信息.
        return PHP_EOL . PHP_EOL . trim($text);
    }


    /**
     * @param string $error
     * @throws InvalidTreeMarkdownException
     */
    public function error(string $error) : void
    {
        $id = $this->doc->id;
        $index = $this->index;

        throw new InvalidTreeMarkdownException(
            "parse markdown document$id failed at line $index: $error"
        );
    }

    public function switchBranch(Branch $branch) : void
    {
        $this->currentBranch = $branch;
        $this->currentSection = $this->sections[$branch->orderId];
    }

    public function getLineMode(int $index): int
    {
        return $this->lineModes[$index] ?? MDParser::LINE_EMPTY;
    }
}