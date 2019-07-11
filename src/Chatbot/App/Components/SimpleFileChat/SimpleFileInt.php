<?php


namespace Commune\Chatbot\App\Components\SimpleFileChat;


use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsIntent;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Illuminate\Support\Str;

/**
 * 通过
 */
class SimpleFileInt extends AbsIntent
{
    const STAGE_PREFIX = '_section_';

    const CONTEXT_TAGS = [Definition::TAG_CONFIGURE];

    /**
     * @var string
     */
    protected $_name;

    protected $_config;

    public function __construct(string $name)
    {
        $this->_name = $name;
        parent::__construct([]);
    }


    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function __onStart(Stage $stage) : Navigator
    {
        $keys = array_keys($this->getConfig()->contents);

        $stages = array_map(function($key){
            return static::STAGE_PREFIX. $key;
        }, $keys);
        $stages[] = 'suggest';

        return $stage->buildTalk()
            ->goStagePipes($stages);
    }


    public function __onSuggest(Stage $stage) : Navigator
    {
        $option = $this->getDef()->getFileChatConfig();
        $suggestions = $option->suggestions;

        if (empty($suggestions)) {
            return $stage->dialog->fulfill();
        }


        $suggestions = $this->buildSuggestions();

        return $stage->component(
            (new Menu(
                $option->groupOption->question,
                $suggestions,
                function(
                    string $context,
                    Dialog $dialog,
                    int $index
                ) {
                    return $dialog
                        ->redirect
                        ->replaceTo($context, Redirect::NODE_LEVEL);
                },
                // fallback 输入任何值都返回.
                [$this, 'goFulfill']
            ))
            ->defaultChoice(null)
        );
    }

    public function goFulfill(Dialog $dialog) : Navigator
    {
        return $dialog->fulfill();
    }

    protected function getConfig() : FileChatConfig
    {
        return $this->_config
            ?? $this->_config = $this->getDef()->getFileChatConfig();
    }

    protected function buildSuggestions() : array
    {

        $config = $this->getConfig();
        $groupOption = $config->groupOption;

        $id = $groupOption->id;
        $alias = $groupOption->intentAlias;

        // 没有猜您想问, 直接退出.
        $suggestions = $config->suggestions;

        // 参数准备.
        $repo = $this->getSession()->contextRepo;

        // 与整个group 的默认配置合并.
        foreach ($config->groupOption->defaultSuggestions as $key => $suggestion) {
            $suggestions[$key] = $suggestion;
        }

        $self = $this->getName();
        $secs = explode('.', $self);
        $last = array_pop($secs);
        $prefix = implode('.', $secs) . '.';

        // 特殊字符.
        if (in_array('./', $suggestions)) {
            $names = $repo->getNamesByDomain($prefix);

            foreach ($names as $name) {
                if (Str::startsWith($name, $prefix)) {
                    $name = str_replace($prefix, '', $name);

                    if ($name != $last && false === strpos('.', $name)) {
                        $suggestions[] = $name;
                    }
                }
            }
        }


        $optionSuggestions = [];

        // 避免重复.
        $loaded = [];
        foreach ($suggestions as $index => $suggestion) {

            if (is_callable($suggestion)) {
                $optionSuggestions[$index] = $suggestion;
                continue;
            }

            if (!is_string($suggestion)) {
                throw new ConfigureException(
                    __METHOD__
                    . ' suggestion should only be callable or string'
                );
            }

            // 允许 alias
            if (array_key_exists($suggestion, $alias)) {
                $suggestion = $alias[$suggestion];
            }

            if (isset($loaded[$suggestion])) {
                continue;
            }

            if ($suggestion === './') {
                continue;
            }

            // 给出参数直接就是 intent name
            if ($repo->has($suggestion)) {
                $optionSuggestions[$index] = $suggestion;
                $loaded[$suggestion] = true;
                continue;
            }

            // 省略了 sfi.id  开头
            if ($repo->has($name = "sfi.$id.".$suggestion)) {
                $optionSuggestions[$index] = $name;
                $loaded[$suggestion] = true;
                continue;
            }

            $name = $this->getName();
            $secs = explode('.', $name);
            array_pop($secs);
            $newName = implode('.', $secs) . '.' . $suggestion;

            // 在同一个目录下.
            if ($repo->has($newName)) {
                $optionSuggestions[$index] = $newName;
                $loaded[$suggestion] = true;
                continue;
            }
        }

        return $optionSuggestions;
    }

    public static function __depend(Depending $depending): void
    {
    }


    public function __exiting(Exiting $listener): void
    {
    }

    /**
     * @return SimpleFileIntDefinition
     */
    public function getDef(): Definition
    {
        return IntentRegistrar::getIns()->get($this->getName());
    }


    public function readSection(Stage $builder, $index) : Navigator
    {
        $contents = $this->getConfig()->contents;
        $index = intval($index);
        $content = $contents[$index];

        $builder = $builder
            ->buildTalk()
            ->info(trim($content));

        if (!isset($contents[$index + 1])) {
            return $builder->next();
        }

        return $builder
            ->info('ask.continue')
            ->wait()
            ->hearing()
            ->isAnyIntent()
            ->end(function(Dialog $dialog){
                return $dialog->next();
            });
    }

    public function __call($name, $arguments)
    {
        $prefix = Context::STAGE_METHOD_PREFIX . static::STAGE_PREFIX;

        if (Str::startsWith($name, $prefix)) {
            $index = substr($name, strlen($prefix));

            return $this->readSection($arguments[0], $index);
        }

        throw new \BadMethodCallException("method $name not found");
    }


    public function __sleep(): array
    {
        $fields = parent::__sleep();
        $fields[] = '_name';
        return $fields;
    }

}