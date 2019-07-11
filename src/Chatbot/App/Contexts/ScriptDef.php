<?php


namespace Commune\Chatbot\App\Contexts;

use Commune\Chatbot\App\Callables\Actions\ToNext;
use Commune\Chatbot\App\Traits\AskContinueTrait;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Speech;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Illuminate\Support\Str;


/**
 * 脚本类 context.
 * 简单来说, 就像文字游戏, 给用户若干段话, 用户输入任何信息都继续.
 * 这样可以让用户一段段地阅读.
 *
 */
abstract class ScriptDef extends OOContext
{
    const DESCRIPTION = '需要为脚本拟个介绍';

    protected $_want_continue = 'dialog.continue';

    /**
     * @var Speech
     */
    protected $_speech;

    /**
     * 所有 stage 共享的中间件.
     * 用于定义公共操作. 比如必须登录.
     * 也可以不定义.
     *
     * @param Stage $stage
     */
    abstract public function __staging(Stage $stage) : void;

    /**
     * 所有 hearing 共享的中间件.
     * 用于定义公共的hearing, 比如闲聊啥的.
     *
     * @param Hearing $hearing
     */
    abstract public function __hearing(Hearing $hearing) : void;

    /**
     * 所有段落都结束之后的最终操作.
     * @param Stage $stage
     * @return Navigator
     */
    abstract public function __onFinal(Stage $stage) : Navigator;

    /**
     * 脚本的内容. 每个key 对应一个 stage
     *
     * key => array 表示这个 stage 会发出多段 Message, 而非 array 的值表示一段message
     *
     * message 允许三种类型:
     * - callable, 会被dialog 调用.
     * - 可以转成string 的值.
     * - 直接就是 Message
     * - 也可以是某个方法的名字. 这样可以用方法来替代一个stage
     *
     * @return string[]
     */
    abstract public static function getScripts() : array;

    abstract public function getSlots() : array;

    public function __onStart(Stage $stage): Navigator
    {
        $stages = static::getScriptStages();
        $stages[] = 'final';
        return $stage->buildTalk()->goStagePipes($stages);
    }



    protected static function getScriptStages() : array
    {
        $keys = array_keys(static::getScripts());
        $stages =  array_map(function($key){
            return "_section_$key";
        }, $keys);
        return $stages;
    }


    public static function buildDefinition(): Definition
    {
        $def = parent::buildDefinition();
        $stages = static::getScriptStages();

        foreach ($stages as $stageName) {
            if (!$def->hasStage($stageName)) {
                $def->setStage(
                    $stageName,
                    function (Stage $stage) use ($stageName): Navigator {

                        $self = $stage->self;
                        $method = Context::STAGE_METHOD_PREFIX . $stageName;

                        return call_user_func([$self, $method], $stage);
                    }
                );
            }
        }

        return $def;
    }

    protected function runSection(Stage $stage, string $index) : Navigator
    {

        $script = static::getScripts()[$index] ?? null;

        if (is_null($script)) {
            throw new ConfigureException(
                __METHOD__
                . " wrong index $index"
            );
        }

        if (is_string($script) && method_exists($this, $script)) {
            return $this->{$script}($stage);
        }

        return $stage->talk(function(Dialog $dialog) use ($index, $script) {

            if (!is_array($script)) {
                $script = [ $script ];
            }

            foreach ($script as $message) {
                $this->sendMessage($dialog, $message);
            }
            $this->getSpeech($dialog)->info($this->_want_continue);
            return $dialog->wait();

        }, [$this, 'toNext']);

    }

    public function toNext(Dialog $dialog, Message $message) : Navigator
    {
        return $dialog->hear($message)
            // 默认任何输入为空才会返回.
            // 可以在 hearing 里定义不同的操作.
            ->isEmpty(new ToNext())
            ->end();
    }

    /**
     * @param Dialog $dialog
     * @param string|callable|Message $message
     */
    protected function sendMessage(Dialog $dialog, $message) : void
    {
        if ($message instanceof Message) {
            $dialog->session->conversation->reply($message);
            return;
        }

        if (is_callable($message)) {
            $dialog->app->callContextInterceptor($this, $message);
            return;
        }

        $this->getSpeech($dialog)->info(strval($message));
    }

    protected function getSpeech(Dialog $dialog) : Speech
    {
        return $this->_speech
            ?? $this->_speech = $dialog->say($this->getSlots());
    }

    public function __call($name, $arguments)
    {
        $prefix = Context::STAGE_METHOD_PREFIX.'_section_';
        if (!Str::startsWith($name, $prefix)) {
            throw new \BadMethodCallException("method $name not found");
        }

        $index = substr($name, strlen($prefix));
        return $this->runSection($arguments[0], $index);

    }

}