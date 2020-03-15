<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Meta;


/**
 * 各种元数据的仓库
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Registrar
{
    /*--------- properties ---------*/

    public function id() : string;

    public function metaType() : string;

    /*--------- meta ---------*/

    /**
     * @param string $id
     * @return bool
     */
    public function hasMeta(string $id) : bool;

    /**
     * @param string $id
     * @return Meta|null
     */
    public function findMeta(string $id) : ? Meta;

    /**
     * @param string $prefix
     * @return array
     */
    public function getMetas(string $prefix = '') : array;

    /**
     * @param string $prefix
     * @return array
     */
    public function getMetaNames(string $prefix = '') : array;

    /**
     * @param Meta $meta
     */
    public function saveMeta(Meta $meta) : void;

    /**
     * 用 yield 遍历
     * @param string $prefix
     * @return \Generator
     */
    public function eachMetaOf(string $prefix = '') : \Generator;

    /**
     * @param string $prefix
     * @return int
     */
    public function countMetas(string $prefix = '') : int;

    /**
     * @param string $metaId
     */
    public function deleteMeta(string $metaId) : void;

    /*--------- registrar relation ---------*/

    /**
     * 父仓库
     * @return Registrar|null
     */
    public function parent() : ? Registrar;

    public function getSubRegistrars() : array;

    public function hasSubRegistrar(string $id) : bool;

    public function findSubRegistrar(string $id) : ? Registrar;

}