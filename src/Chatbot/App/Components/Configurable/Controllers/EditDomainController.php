<?php


namespace Commune\Chatbot\App\Components\Configurable\Controllers;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\App\Components\OptionEdit\OptionEditor;
use Commune\Chatbot\App\Components\Configurable\Drivers\DomainConfigRepository;

/**
 * @property string $domainName
 * @property DomainConfig|null $config
 * @property OptionEditor $editing
 */
class EditDomainController extends Controller
{
    const DESCRIPTION = '查看或编辑某一个模块(不存在则会添加)';

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->build()
            ->askVerbose('请输入 domain 的名称.')
            ->callback()
            ->hearing()
            ->isAnswer(function(
                Dialog $dialog,
                Answer $answer,
                DomainConfigRepository $repo
            ){
                $name = $answer->toResult();
                if (empty($name)) {
                    $dialog->say()
                        ->warning("domain name 不能为空.");
                    return $dialog->restart();
                }

                $this->domainName = $name;
                if (!$repo->has($name)) {
                    $dialog->say()
                        ->info('configurable.domain.notExists', [
                            '%name%'=> $name
                        ]);

                    return $dialog->goStage('create');
                }


                return $dialog->goStage('edit');


            })
            ->end(function(Dialog $dialog){

                $dialog->say()
                    ->warning("无法理解的内容");

                return $dialog->restart();

            });

    }

    public function __onCreate(Stage $stage) : Navigator
    {
        return $stage->build()
            ->askConfirm('确认创建模块 '.$this->domainName.' 吗?')
            ->callback()
                ->hearing()
                    ->isChoice(
                        1,
                        function(Dialog $dialog, DomainConfigRepository $repo){
                            $repo->update(new DomainConfig([
                                'domain' => $this->domainName
                            ]));

                            return $dialog->goStage('edit');

                        }
                    )
                    ->isChoice(0, function(Dialog $dialog) {
                        return $dialog->restart();

                    })->end();
    }

    public function __onEdit(Stage $stage) : Navigator
    {
        /**
         * @var DomainConfigRepository $repo
         */
        $repo = $stage->dialog->app->make(DomainConfigRepository::class);
        $domain = $repo->get($this->domainName);
        return $stage->dependOn(
            OptionEditor::make($domain),
            function(Dialog $dialog, OptionEditor $editor) use ($repo){
                $option = $editor->option;
                if ($editor->changed && $option instanceof DomainConfig) {
                    $repo->update($option);
                }
                return $dialog->restart();
            }
        );
    }

    public function __onAskSave(Stage $stage) : Navigator
    {
        return $stage->build()
            ->askConfirm('需要保存修改吗?')
            ->callback()
                ->hearing()
                ->isChoice(1, function(Dialog $dialog){
                    return $dialog->goStage('save');
                })
                ->isChoice(0, function(Dialog $dialog) {
                    $this->editing = null;
                    return $dialog->fulfill();

                })->end();

    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onCancel(function(Dialog $dialog, Context $callback) {
            if ($callback instanceof OptionEditor) {
                $this->editing = $callback;
                return $dialog->goStage('askSave');
            }
            return null;
        });
    }
}