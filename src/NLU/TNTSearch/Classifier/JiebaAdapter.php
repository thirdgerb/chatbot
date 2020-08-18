<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\TNTSearch\Classifier;

use Commune\NLU\Jieba\JiebaTokenizer;
use TeamTNT\TNTSearch\Support\TokenizerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JiebaAdapter implements TokenizerInterface
{
    /**
     * @var JiebaTokenizer
     */
    protected $tokenizer;

    /**
     * JiebaAdapter constructor.
     * @param JiebaTokenizer $tokenizer
     */
    public function __construct(JiebaTokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function tokenize($text, $stopwords)
    {
        return $this->tokenizer->tokenize(
            $text,
            is_array($stopwords) ? $stopwords : null
        );
    }


}