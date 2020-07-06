<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Trans;

use Commune\Contracts\Trans\Translator;
use Commune\Support\Registry\Category;
use Commune\Support\Utils\TypeUtils;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SymfonyTranslatorAdapter implements Translator
{
    /**
     * @var MessageFormatter
     */
    protected $formatter;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var string
     */
    protected $defaultDomain;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /*---- cached ----*/

    protected $intlTemps = [];

    protected $temps = [];


    public function __construct(Category $category, LoggerInterface $logger,  string $locale, string $domain)
    {
        $this->logger = $logger;
        $this->category = $category;
        $this->formatter = new MessageFormatter();
        $this->defaultDomain = $domain;
        $this->defaultLocale = $locale;

        $this->init();
    }

    protected function init() : void
    {
        foreach ($this->category->eachId() as $id) {
            /**
             * @var TransOption $option
             */
            $option = $this->category->find($id);
            $this->cacheOption($option);
        }
    }

    protected function cacheOption(TransOption $option) : void
    {
        $temp = $option->temp;
        $domain = $option->domain;
        $locale = $option->locale;
        $intl = $option->intl;
        $id = $option->transId;

        if ($intl) {
            $this->intlTemps[$locale][$domain][$id] = $temp;
        } else {
            $this->temps[$locale][$domain][$id] = $temp;
        }
    }

    public function trans(
        $id,
        array $parameters = [],
        string $domain = null,
        string $lang = null
    ): string
    {
        $id = trim(strval($id));
        $domain = $domain ?? $this->defaultDomain;
        $locale = $locale ?? $this->defaultLocale;

        $parameters = Arr::dot($parameters);
        $parameters = array_filter($parameters, [TypeUtils::class, 'maybeString']);

        return $this->transByIntlTemp($id, $locale, $domain, $parameters)
            ?? $this->transByTemp($id, $locale, $domain, $parameters);
    }

    protected function getPrefix(string $locale, string $domain) : string
    {
        return "$locale:$domain:";
    }

    protected function transByIntlTemp(string $id, string $locale, string $domain, array $parameters) : ? string
    {
        // 模板不存在, 则退出.
        $temp = $this->intlTemps[$locale][$domain][$id] ?? null;


        if (is_null($temp)) {
            return null;
        }

        return extension_loaded('intl')
            ? $this->formatter->formatIntl($temp, $locale, $parameters)
            : self::mustacheTrans($this->formatter, $temp, $locale, $parameters);
    }

    protected function transByTemp(string $id, string $locale, string $domain, array $parameters) : string
    {
        $temp = $this->temps[$locale][$domain][$id] ?? $id;
        return self::mustacheTrans($this->formatter, $temp, $locale, $parameters);
    }

    public static function mustacheTrans(
        MessageFormatterInterface $formatter,
        string $text,
        string $locale,
        array $parameters
    ) : string
    {
        return $formatter->format($text, $locale, self::addMustache($parameters));
    }

    public static function addMustache(array $parameters) : array
    {
        $result = [];
        foreach ($parameters as $key => $parameter) {
            $key = '{' . $key. '}';
            $result[$key] = $parameter;
        }

        return $result;
    }

    public function saveMessages(
        array $messages,
        string $locale = null,
        string $domain = null,
        bool $intl = true,
        bool $force = false
    ): void
    {
        $locale = $locale ?? $this->defaultLocale;
        $domain = $domain ?? $this->defaultDomain;
        $prefix = $this->getPrefix($locale, $domain);
        $messages = Arr::dot($messages);

        foreach ($messages as $id => $template) {

            $transOption = new TransOption([
                'id' => $prefix . $id,
                'transId' => $id,
                'locale' => $locale,
                'domain' => $domain,
                'temp' => $template,
                'intl' => $intl
            ]);

            $this->cacheOption($transOption);
            $this->category->save($transOption, !$force);
        }
    }


}