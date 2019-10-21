<?php


namespace Commune\Components\SimpleWiki\Libraries;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\Actions\Talker;
use Commune\Chatbot\App\Callables\StageComponents\AskContinue;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Intent\AbsIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\SimpleWiki\Options\WikiOption;
use Commune\Support\Utils\StringUtils;
use Illuminate\Support\Str;

/**
 * 通过读取 WikiOption 配置, 自动生成 "连续对话" + "猜你想问" 形式的知识库.
 *
 * 本功能在具体业务场景下未必完全有用, 主要作为可配置多轮对话的开发样板, 提供给开发者参考.
 */
class SimpleWikiInt extends AbsIntent
{

    const CONTEXT_TAGS = [Definition::TAG_CONFIGURE];

    /**
     * @var string
     */
    protected $_name;


    /*---- cached ----*/

    protected $definition;

    protected $registrar;

    public function __construct(string $name)
    {
        $this->_name = $name;
        parent::__construct([]);
    }

    public function __sleep(): array
    {
        $fields = parent::__sleep();
        $fields[] = '_name';
        return $fields;
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }


    public function navigate(Dialog $dialog): ? Navigator
    {
        if ($dialog->currentContext() instanceof SimpleWikiInt) {
            if ($dialog->currentContext()->nameEquals($this->getName())) {
                return $dialog->restart();
            }

            return $dialog
                ->redirect
                ->dependOn($this);
        }

        return $dialog->redirect->sleepTo($this);
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function __staging(Stage $stage) : void
    {
        $stage->onIntended(function(Dialog $dialog, Message $message) {
            return $dialog->repeat();
        });

    }

    public function __hearing(Hearing $hearing) : void
    {
        $group = $this->getDef()->getGroupConfig()->getId();
        $hearing
            ->runIntentIn(
                [WikiOption::INTENT_NAME_PREFIX . '.' . $group . '.']
            )
            ->runAnyIntent();
    }

    public function __onStart(Stage $stage) : Navigator
    {
        $def = $this->getDef();
        $replies = $def->getConfig()->replies;
        $msgPrefix = $def->getGroupConfig()->messagePrefix;

        $replies = array_filter($replies, function($i){
            return !empty($i);
        });

        $scripts = array_map(function($info) use ($msgPrefix){
            $msgPrefix = trim($msgPrefix, '.');
            $speech = Talker::say();

            // 如果以 ~ 开头, 则认为就是普通的文本.
            if ($info[0] === '~') {
                return $speech->info(substr($info, 1));
            }

            $infos = explode('|', $info);
            foreach ($infos as $info) {
                $info = trim($info, '.');
                $speech = $speech->info("$msgPrefix.$info");
            }
            return $speech;
        }, $replies);

        $askContinue = new AskContinue($scripts);

        return $stage->component(
            $askContinue
                ->onHelp()
                ->onFinal(Redirector::goStage('final'))
        );
    }

    public function __onFinal(Stage $stage) : Navigator
    {
        $group = $this->getDef()->getGroupConfig();

        $suggestions = $this->buildSuggestions();
        if (empty($suggestions)) {
            return $stage->dialog->fulfill();
        }

        $component = new Menu(
            $group->question,
            $suggestions
        );

        $component = $component->onRedirect(
            function(
                Context $context,
                Dialog $dialog,
                string $contextName
            ) : Navigator {

                if ($this->getRepo()->hasDef($contextName)) {
                    return $dialog->redirect->dependOn($contextName);
                }

                return $dialog
                    ->redirect
                    ->replaceTo($contextName, Redirect::NODE_LEVEL);
            }
        );

        return $stage->component($component);
    }

    protected function buildSuggestions() : array
    {
        $def = $this->getDef();
        $config = $def->getConfig();
        $group = $def->getGroupConfig();
        $intentName = $config->intentName;

        $groupId = $group->id;
        $alias = $group->intentAlias;


        // 参数准备.
        $repo = $this->getRepo();

        // 合并当前配置和默认配置.
        $suggestions = $config->suggestions;
        $suggestions = $suggestions + $group->defaultSuggestions;
        if (empty($suggestions)) {
            return [];
        }

        // 最终输出的 suggestions
        $optionSuggestions = [];

        // 避免重复.
        $loaded = [];
        foreach ($suggestions as $index => $suggestion) {

            // callable 方法.
            if (is_callable($suggestion)) {
                if (is_string($index)) {
                    $optionSuggestions[$index] = $suggestion;
                }
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

            // 用 . 开头.
            if ($suggestion[0] === '.') {
                $current = substr(
                    $intentName,
                    strlen(WikiOption::INTENT_NAME_PREFIX . '.' . $groupId . '.')
                );
                try {

                    $suggestion = StringUtils::dotPathParser(
                        $current,
                        $suggestion
                    );
                } catch (\InvalidArgumentException $e) {
                    continue;
                }
            }

            if ($suggestion[0] === '/') {
                $suggestion = substr($suggestion, 1);
                $suggestion = WikiOption::INTENT_NAME_PREFIX
                    . '.'
                    . $groupId
                    . '.'
                    . $suggestion;
            }

            if (isset($loaded[$suggestion])) {
                continue;
            }

            if (Str::endsWith($suggestion, '.*')) {
                $prefix = $config->getPrefix() . '.';
                $ids = $repo->getDefNamesByDomain($prefix);
                foreach ($ids as $id) {
                    // 不用自己
                    if ($id === $this->getName()) {
                        continue;
                    }

                    // 只用同级目录
                    if (
                        strstr(
                            str_replace($prefix, '', $id),
                            '.'
                        )
                    ) {
                        continue;
                    }
                    $optionSuggestions[] = $id;
                    $loaded[$id] = true;
                }
                continue;
            }

            // 给出参数直接就是 intent name
            if ($repo->hasDef($suggestion)) {
                $optionSuggestions[] = $suggestion;
                $loaded[$suggestion] = true;
                continue;
            }

            // 省略了 sfi.groupId  开头
            if ($repo->hasDef($name = WikiOption::INTENT_NAME_PREFIX. ".$groupId.".$suggestion)) {
                $optionSuggestions[] = $name;
                $loaded[$suggestion] = true;
                continue;
            }
        }

        return $optionSuggestions;
    }

    /**
     * @return SimpleWikiDefinition
     */
    public function getDef(): Definition
    {
        return $this->definition
            ?? $this->definition = $this->getRepo()
                ->getDef($this->getName());
    }

    public function getRepo() : SimpleWikiRegistrar
    {
        return $this->registrar
            ?? $this->registrar = $this
                ->getSession()
                ->conversation->make(SimpleWikiRegistrar::class);
    }


}