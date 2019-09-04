<?php


namespace Commune\Chatbot\App\Memories;

use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Scope;
use Illuminate\Support\Str;


/**
 * 与记忆结合的问卷调查组件.
 * 算是一个示例.
 *
 * @property bool $finish
 * @property array $answers
 * @property int $nowQuestion
 * @property-read int $questionTotal
 */
abstract class Questionnaire extends MemorialTask
{
    /*------- 用户配置. -------*/

    const DESCRIPTION = "请撰写问题说明";

    // 默认用户级别的.
    const SCOPES = [Scope::USER_ID];

    /*------- 系统设置 -------*/

    const QUESTION_STAGE_PREFIX = '_question_';

    /*------- 缓存参数 -------*/

    /**
     * @var int
     */
    protected $_total;


    protected function init() : array
    {
        return [
            'finish' => false,
            'answers' => [],
            'nowQuestion' => 0
        ];
    }


    /**
     * @return array   格式是  [
     *  '问题' => [ '选项1', '选项2','选项3', ],
     * ]
     */
    abstract public static function getQuestionDefinition() : array;

    abstract public function doFinal(Dialog $dialog) : ? Navigator;

    abstract protected function defaultFallback() : ? callable ;

    abstract protected function specialHandler(Dialog $dialog, int $questionIndex, $choice) : ? Navigator;

    /**
     * 拿到结果之后的处理.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onFinal(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->interceptor([$this, 'doFinal'])
            ->action(function(Dialog $dialog) : Navigator{
                $this->finish = true;
                return $dialog->fulfill();
            });
    }


    protected function doAsk(Stage $stage) : Navigator
    {
        $definition = $this->getQuestionDefinition();
        $questions = array_keys($definition);
        $answers = array_values($definition);

        $index = $this->nowQuestion;
        return $stage->buildTalk()
            ->askChoose(
                $this->wrapQuestion($questions[$index]),
                $this->wrapAnswers($answers[$index])
            )
            ->wait()
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Choice $choice) {
                $answer = $choice->getChoice();
                $answers = $this->answers;
                $answers[$this->nowQuestion] = $answer;
                $this->answers = $answers;

                // 可以在这里做一些特殊的处理.
                $navigator = $this->specialHandler($dialog, $this->nowQuestion, $answer);
                // 否则按题目顺序前进.
                return $navigator ?? $this->next($dialog);
            })
            ->end($this->defaultFallback());
    }

    /**
     * 对问题进行格式化的包装.
     * @param string $question
     * @return string
     */
    protected function wrapQuestion(string $question) : string
    {
        $id = $this->nowQuestion + 1;
        $total = $this->questionTotal;
        return "第 $id/$total 题: $question";
    }

    /**
     * @param Dialog $dialog
     * @param int|null $index
     * @return Navigator
     */
    protected function next(Dialog $dialog, $index = null) : Navigator
    {
        $index = $index ?? $this->nowQuestion + 1;
        // 最后一题
        if ($index >= $this->questionTotal) {
            return $dialog->goStage('final');
        }
        return $dialog->goStage(static::QUESTION_STAGE_PREFIX . $index);
    }


    public function __getQuestionTotal()
    {
        return $this->_total ?? $this->_total = count($this->getQuestionDefinition());
    }

    /**
     * 对选项进行统一封装
     * @param array $answers
     * @return array
     */
    protected function wrapAnswers(array $answers) : array
    {
        return $answers;
    }

    public static function buildDefinition(): Definition
    {
        $def = parent::buildDefinition();
        $questions = static::getQuestionDefinition();

        $i = 0;
        foreach ($questions as $answers) {

            $def->setStage(
                $stageName = static::QUESTION_STAGE_PREFIX.$i,
                function(Stage $stage) use ($stageName): Navigator {
                    return call_user_func(
                        [$stage->self, static::STAGE_METHOD_PREFIX . $stageName],
                        $stage
                    );
                }
            );
            $i++;
        }

        return $def;
    }

    public function __call($name, $arguments)
    {
        $method = static::STAGE_METHOD_PREFIX . static::QUESTION_STAGE_PREFIX;
        if (Str::startsWith($name, $method)) {
            $questionIndex = substr($name, strlen($method));
            $index = intval($questionIndex);
            $this->nowQuestion = $index;
            return call_user_func_array([$this, 'doAsk'], $arguments);

        }

        throw new \BadMethodCallException("method $name not exists");
    }

}