<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Option;

use Commune\Support\DI\Injectable;
use Commune\Support\Struct\Struct;

/**
 * 将数组转化为结构体. 
 * 
 * 接受数组作为数据, 并通过强类型来调用数组中的数据.
 * 可以根据约定, 将子属性转化为同样的对象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Option extends Struct, Injectable
{
    const TITLE_FUNC = 'getTitle';
    const DESC_FUNC = 'getDescription';

    /**
     * 当option 作为列表元素被使用的时候
     * getId() 能让我们知道哪个是哪个
     * 所以最好每个可作为列表元素的, 都重写本方法.
     *
     * when there are list of options,
     * getId() could identify them
     *
     * @return string|mixed
     */
    public function getId() : string;


    /**
     * 默认样板数据. 为真实数据提供默认值.
     *
     * default data of option
     * @return array
     */
    public static function stub() : array;


    /**
     * 使用ID + stub, 创建一个 option对象.
     *
     * @param $id
     * @param array $data
     * @return static
     */
    public static function createById($id, array $data = []) : Option;

    /*------- methods -------*/

    /**
     * 合并一个数组到当前option, 生成一个新的option对象.
     *
     * @param array $data
     * @return static
     */
    public function merge(array $data);


    /*------- 转型 -------*/

    /**
     * option 数据的哈希, 可以用来比较异同.
     * @return string
     */
    public function getHash() : string;

    /*------- 参数配置 -------*/

    /**
     * @return string
     */
    public function getTitle() : string;

    /**
     * @return string
     */
    public function getDescription() : string;


}