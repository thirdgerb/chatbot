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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class MDAnalyser
{

    /**
     * @var MDParser
     */
    protected $parser;

    /**
     * MDAnalyser constructor.
     * @param MDParser $parser
     */
    public function __construct(MDParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param int $index
     * @param string $line
     * @return int|null         mode
     */
    abstract public function __invoke(int $index, string $line) : ? int;
}