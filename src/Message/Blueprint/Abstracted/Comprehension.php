<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Abstracted;

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;

/**
 * 对输入消息的抽象理解. 保存各种类型的理解信息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Intent $intent                    意图和实体理解. 可以针对任何输入信息
 * @property-read Choice $choice                    选项. 可以来自各种输入信息
 * @property-read CmdStr $command                   命令. 通常源消息是字符串.
 * @property-read Emotion $emotion                  情感. 通常来自 NLU
 * @property-read Recognition $recognition          对多媒体信息的识别.
 * @property-read Tokenization $tokenization        分词, 关键词.
 * @property-read SoundLike $soundLike              语言模块. 各种语言有不同的识别结果.
 */
interface Comprehension extends ArrayAndJsonAble, BabelSerializable
{

}