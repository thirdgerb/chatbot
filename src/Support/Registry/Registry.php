<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry;

use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Exceptions\CategoryNotFoundException;


/**
 * Struct 类型的数据表, 用于项目的基础配置读取.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Registry
{
    /*---- meta ----*/

    /**
     * 注册一个 struct 分类的元数据.
     * @param CategoryOption $meta
     */
    public function registerCategory(CategoryOption $meta) : void;

    /**
     * 获取一个 struct 分类的元数据. 不存在会抛出异常.
     *
     * @param string $categoryName
     * @return CategoryOption
     * @throws CategoryNotFoundException
     */
    public function getCategoryOption(string $categoryName) :  CategoryOption;

    /**
     * 检查分类是否存在.
     *
     * @param string $categoryName
     * @return bool
     */
    public function hasCategory(string $categoryName) : bool ;


    /**
     * @param string $categoryName
     * @return Category
     * @throws CategoryNotFoundException
     */
    public function getCategory(string $categoryName) : Category;

    /**
     * @return \Generator
     */
    public function eachCategory() : \Generator;

}