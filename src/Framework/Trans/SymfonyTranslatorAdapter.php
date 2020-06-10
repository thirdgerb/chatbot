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
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Formatter\IntlFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SymfonyTranslatorAdapter implements Translator
{
    /**
     * @var IntlFormatter
     */
    protected $intl;

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
        $this->intl = new IntlFormatter();
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
        $len = strlen($this->getPrefix($locale, $domain));
        $id = substr($option->id, $len);

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
        $id = strval($id);
        $domain = $domain ?? $this->defaultDomain;
        $locale = $locale ?? $this->defaultLocale;

        return $this->transByTemp($id, $locale, $domain, $parameters)
            ?? $this->transByIntl($id, $locale, $domain, $parameters);
    }

    protected function getPrefix(string $locale, string $domain) : string
    {
        return "$locale:$domain:";
    }

    protected function transByIntl(string $id, string $locale, string $domain, array $parameters) : string
    {
        $temp = $this->intlTemps[$locale][$domain][$id] ?? $id;
        return extension_loaded('intl')
            ? $this->intl->formatIntl($temp, $locale, $parameters)
            : $this->transById($temp, $parameters);
    }

    protected function transByTemp(string $id, string $locale, string $domain, array $parameters) : ? string
    {
        $temp = $this->temps[$locale][$domain][$id] ?? null;

        return isset($temp)
            ? $this->transById($temp, $parameters)
            : null;
    }

    protected function transById(string $id, array $parameters) : string
    {
        $parameters = Arr::dot($parameters);
        $replaces = [];
        foreach ($parameters as $key => $val) {
            if (is_scalar($val)) {
                $replaces["\{$key\}"] = strval($val);
            }
        }

        return str_replace(array_keys($replaces), array_values($replaces), $id);
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
                'locale' => $locale,
                'domain' => $domain,
                'temp' => $template,
                'intl' => $intl
            ]);

            if ($this->category->save($transOption, !$force)) {
                $this->cacheOption($transOption);
            } elseif ($force) {
                $this->logger->error('save trans option fail : ' . $transOption->toJson());
            }
        }
    }


}