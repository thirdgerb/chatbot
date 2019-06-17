<?php

/**
 * Class Translator
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

/**
 * 参考symfony, 对translator 做了一个 interface
 *
 * @see \Symfony\Contracts\Translation\TranslatorInterface
 *
 * Interface Translator
 * @package Commune\Chatbot\Contracts
 *
 */
interface Translator
{
    const MESSAGE_DOMAIN = 'messages';
    const MARKER = '%';

    const FORMAT_YAML = 'yaml';
    const FORMAT_JSON = 'json';
    const FORMAT_CSV = 'csv';
    const FORMAT_XLIFF = 'xliff';
    const FORMAT_PHP = 'php';

    /*-------- default speech --------*/


    /**
     * Translates the given message.
     *
     * When a number is provided as a parameter named "%count%", the message is parsed for plural
     * forms and a translation is chosen according to this number using the following rules:
     *
     * Given a message with different plural translations separated by a
     * pipe (|), this method returns the correct portion of the message based
     * on the given number, locale and the pluralization rules in the message
     * itself.
     *
     * The message supports two different types of pluralization rules:
     *
     * interval: {0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples
     * indexed:  There is one apple|There are %count% apples
     *
     * The indexed solution can also contain labels (e.g. one: There is one apple).
     * This is purely for making the translations more clear - it does not
     * affect the functionality.
     *
     * The two methods can also be mixed:
     *     {0} There are no apples|one: There is one apple|more: There are %count% apples
     *
     * An interval can represent a finite set of numbers:
     *  {1,2,3,4}
     *
     * An interval can represent numbers between two numbers:
     *  [1, +Inf]
     *  ]-1,2[
     *
     * The left delimiter can be [ (inclusive) or ] (exclusive).
     * The right delimiter can be [ (exclusive) or ] (inclusive).
     * Beside numbers, you can use -Inf and +Inf for the infinite.
     *
     * @see https://en.wikipedia.org/wiki/ISO_31-11
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function trans(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null
    ) : string;

    /**
     * @param mixed $resource 比如文件名. 和loader有关.
     * @param string|null $locale 默认用配置的
     * @param string|null $domain 默认是messages
     *
     */
    public function addResource(
        $resource,
        string $locale = null,
        string $domain = null
    ) : void;


}