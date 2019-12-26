<?php

namespace Commune\Chatbot\OOHost\NLU\Contracts;

/**
 * 语料库. 用于标注意图.
 * 管理 example 例句和 entity 词典.
 */
interface Corpus
{

    /**
     * 同步所有的 corpus 配置 (有的在存储介质里, 有的是别的组件加载的)
     * 存储到公共的 corpus 存储介质里. ( OptionRepository )
     *
     * @param bool $force
     * @return string
     */
    public function sync(bool $force = false) : string;


    /**
     * 使用 Option 类名获取指定的管理工具
     * @param string $corpusOptionName
     * @return Manager|null
     */
    public function getManager(string $corpusOptionName) : ? Manager;

    /**
     * 意图语料库 的管理工具
     * @return Manager
     */
    public function intentCorpusManager()  : Manager;

    /**
     * 实体词典的管理工具
     * @return Manager
     */
    public function entityDictManager() : Manager;

    /**
     * 同义词词典的管理工具
     * @return Manager
     */
    public function synonymsManager() : Manager;

}