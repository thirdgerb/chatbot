<?php

/**
 * Class TypeTransfer
 * @package Commune\Chatbot\Framework\Support
 */

namespace Commune\Chatbot\Framework\Support;


use Illuminate\Support\Str;

class ChatbotUtils
{
    const JSON_OPTION = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

    public static function getCommandStr(string $text, string $commandMark = '') : ? string
    {
        if (strlen($commandMark) === 0) {
            $command = $text;
        } elseif (Str::startsWith($text, $commandMark) && !Str::startsWith($text, "$commandMark$commandMark")) {
            $command = substr($text, strlen($commandMark));
        } else {
            return null;
        }

        if (empty($command)) {
            return null;
        }

        //命令名称长度不超过20个字符, 如果带参数, 必须要有空格.
        $spacePos = strpos($command, ' ');
        $valid = ($spacePos === 0 && mb_strlen($command) < 20) || $spacePos < 20;

        return $valid ? $command : null;
    }
//
//    public static function parseDefinitionOfSignature(string $signature) : CommandDefinition
//    {
//        list($name, $arguments, $options) = Parser::parse($signature);
//
//        $definition = new CommandDefinition($name);
//        $definition->addArguments($arguments);
//        $definition->addOptions($options);
//        return $definition;
//    }

    /**
     * @see https://www.bbsmax.com/A/Ae5Ry6bAJQ/
     * @param string $str
     * @return string
     */
    public static function sbc2dbc(string $str) : string
    {
        $arr = array(
            '０'=>'0', '１'=>'1', '２'=>'2', '３'=>'3', '４'=>'4','５'=>'5', '６'=>'6', '７'=>'7', '８'=>'8', '９'=>'9',
            'Ａ'=>'A', 'Ｂ'=>'B', 'Ｃ'=>'C', 'Ｄ'=>'D', 'Ｅ'=>'E','Ｆ'=>'F', 'Ｇ'=>'G', 'Ｈ'=>'H', 'Ｉ'=>'I', 'Ｊ'=>'J',
            'Ｋ'=>'K', 'Ｌ'=>'L', 'Ｍ'=>'M', 'Ｎ'=>'N', 'Ｏ'=>'O','Ｐ'=>'P', 'Ｑ'=>'Q', 'Ｒ'=>'R', 'Ｓ'=>'S', 'Ｔ'=>'T',
            'Ｕ'=>'U', 'Ｖ'=>'V', 'Ｗ'=>'W', 'Ｘ'=>'X', 'Ｙ'=>'Y','Ｚ'=>'Z', 'ａ'=>'a', 'ｂ'=>'b', 'ｃ'=>'c', 'ｄ'=>'d',
            'ｅ'=>'e', 'ｆ'=>'f', 'ｇ'=>'g', 'ｈ'=>'h', 'ｉ'=>'i','ｊ'=>'j', 'ｋ'=>'k', 'ｌ'=>'l', 'ｍ'=>'m', 'ｎ'=>'n',
            'ｏ'=>'o', 'ｐ'=>'p', 'ｑ'=>'q', 'ｒ'=>'r', 'ｓ'=>'s', 'ｔ'=>'t', 'ｕ'=>'u', 'ｖ'=>'v', 'ｗ'=>'w', 'ｘ'=>'x',
            'ｙ'=>'y', 'ｚ'=>'z',
            '（'=>'(', '）'=>')', '〔'=>'(', '〕'=>')', '【'=>'[','】'=>']', '〖'=>'[', '〗'=>']', '“'=>'"', '”'=>'"',
            '‘'=>'\'', '｛'=>'{', '｝'=>'}', '《'=>'<','》'=>'>','％'=>'%', '＋'=>'+', '—'=>'-', '－'=>'-',
            '～'=>'~','：'=>':', '。'=>'.', '、'=>',', '，'=>',', '；'=>';', '？'=>'?', '！'=>'!', '…'=>'-',
            '‖'=>'|', '｜'=>'|', '〃'=>'"','　'=>' ', '×'=>'*', '￣'=>'~', '．'=>'.', '＊'=>'*',
            '＆'=>'&','＜'=>'<', '＞'=>'>', '＄'=>'$', '＠'=>'@', '＾'=>'^', '＿'=>'_', '＂'=>'"', '￥'=>'$', '＝'=>'=',
            '＼'=>'\\', '／'=>'/'
        );
        return strtr($str, $arr);
    }


    public static function toString($any) : string
    {
        if (is_string($any)) {
            return $any;
        } elseif (is_null($any)) {
            return 'null';
        } elseif (is_bool($any)) {
            return $any ? 'true' : 'false';
        } elseif (is_object($any) && !method_exists($any, '__toString')) {
            return serialize($any);
        }

        return strval($any);
    }

}