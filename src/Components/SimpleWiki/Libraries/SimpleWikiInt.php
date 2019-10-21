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
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\Utils\StringUtils;
use Illuminate\Support\Str;

/**
 * 通过
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
            return $dialog->restart();
        });

    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing->runAnyIntent();
    }

    public function __onStart(Stage $stage) : Navigator
    {
        $def = $this->getDef();
        $replies = $def->getConfig()->replies;
        $msgPrefix = $def->getGroupConfig()->messagePrefix;


        $scripts = array_map(function($info) use ($msgPrefix){
            $msgPrefix = trim($msgPrefix, '.');

            $infos = explode('|', $info);
            $speech = Talker::say();
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

        // 没有猜您想问, 直接退出.
        $suggestions = $config->suggestions;

        // 参数准备.
        $repo = $this->getRepo();

        // 合并当前配置和默认配置.
        $suggestions = $suggestions + $group->defaultSuggestions;

        // 最终输出的 suggestions
        $optionSuggestions = [];

        // 避免重复.
        $loaded = [];
        foreach ($suggestions as $index => $suggestion) {

            // callable 方法.
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
                $ids = $repo->getDefNamesByDomain($config->getPrefix());
                foreach ($ids as $id) {
                    if ($id === $this->getName()) {
                        continue;
                    }
                    $optionSuggestions[$index] = $id;
                    $loaded[$id] = true;
                }
                continue;
            }

            // 给出参数直接就是 intent name
            if ($repo->hasDef($suggestion)) {
                $optionSuggestions[$index] = $suggestion;
                $loaded[$suggestion] = true;
                continue;
            }

            // 省略了 sfi.groupId  开头
            if ($repo->hasDef($name = WikiOption::INTENT_NAME_PREFIX. ".$groupId.".$suggestion)) {
                $optionSuggestions[$index] = $name;
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