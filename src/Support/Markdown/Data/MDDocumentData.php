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

use Commune\Support\ArrTree\Tree;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $id                  文档的全局唯一ID. 通常根据 path 计算.
 * @property array $tree                 所有子节点序号的树状结构.
 * @property string $rootName            根节点名称.
 *
 * @property string $parser
 *
 *
 */
class MDDocumentData extends AbsOption
{
    const IDENTITY = 'id';

    /**
     * @var MDSectionData[]|null
     */
    protected $_sectionMap;

    public static function stub(): array
    {
        return [
            'id' => '',
            // 根节点名称.
            'rootName' => '',
            // 用序号呈现的树.
            // 通过 orderId 来获取所有的子节点.
            'tree' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

//
//    public function toText() : string
//    {
//        $text = $this->toBlockText();
//
//        $sections = array_map(function(MDSectionData $data) {
//            $text = $data->toText();
//            if (mb_substr($text, -1, 1) === "\n") {
//                $text = mb_substr($text, 0, -1);
//            }
//            return $text;
//
//        }, $this->sections);
//
//        array_unshift($sections, $text);
//        return implode("\n\n", $sections);
//    }


    /*-------- parser --------*/
//
//    public static function createByFilePath(
//        string $id,
//        string $filePath,
//        string $rootPath
//    ) : self
//    {
//        $realPath = realpath($filePath);
//        $realRootPath = realpath($rootPath);
//        if (empty($realPath)) {
//            throw new \InvalidArgumentException("file $filePath not found");
//        }
//
//        if (empty($realRootPath)) {
//            throw new \InvalidArgumentException("root path $rootPath invalid");
//        }
//
//        $info = pathinfo($filePath);
//
//        $basename = $info['filename'];
//        $content = file_get_contents($filePath);
//
//        $relativePath = str_replace($rootPath, '', $realPath);
//        $realPath = trim($relativePath, DIRECTORY_SEPARATOR);
//
//        return static::createByContent(
//            $id,
//            $realPath,
//            $relativePath,
//            $basename,
//            $content
//        );
//    }
//
//    public static function createByContent(
//        string $id,
//        string $filePath,
//        string $relativePath,
//        string $basename,
//        string $content
//    ) : self
//    {
//
//        $lines = explode("\n", $content);
//        $self = new static([
//            'id' => $id,
//            'filePath' => $filePath,
//            'relativePath' => $relativePath,
//            'basename' => $basename,
//        ]);
//
//        // prepare arr tree
//        $tree = new Tree();
//
//        $parser = new MDFileParser(
//            $tree,
//            $self,
//            static::ROOT_NAME
//        );
//
//        $result = $parser->readContent($content);
//        $parser->destroy();
//
//        return $result;
//    }

//
//    public function __destruct()
//    {
//        unset(
//            $this->_commentMap,
//            $this->_content
//        );
//        parent::__destruct();
//    }
}