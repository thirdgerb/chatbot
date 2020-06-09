<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Manager\OptRegistry;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Components\Predefined\Intent\Navigation\CancelInt;
use Commune\Ghost\Context\ACodeContext;
use Commune\Support\Registry\OptRegistry;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $categoryName
 */
class ViewCategory extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'queryNames' => ['categoryName']
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(Stage $stage): Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog, OptRegistry $registry) {

                if (!$registry->hasCategory($this->categoryName)) {
                    return $dialog
                        ->send()
                        ->error("category {cate} not found", ['cate' => $this->categoryName])
                        ->over()
                        ->cancel();
                }

                $config = $registry->getCategory($this->categoryName)->getConfig();

                $name = $config->name;
                $title = $config->title;
                $desc = $config->desc;

                return $dialog
                    ->send()
                    ->info(
                        "当前配置注册表:\n- name: {name}\n- title: {title}\n- desc: {desc}",
                        compact('title', 'name', 'desc')
                    )
                    ->over()
                    ->await()
                    ->askChoose(
                        '请选择命令',
                        [
                            $this->getStage('config'),
                            CancelInt::makeUcl(),
                        ]
                    );
            });
    }


    /**
     * @param Stage $stage
     * @return Stage
     *
     * @title 查看配置
     * @desc 查看详细配置
     */
    public function __on_config(Stage $stage): Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog, OptRegistry $registry) {


                return $dialog
                    ->send()
                    ->info(
                        $registry
                            ->getCategory($this->categoryName)
                            ->getConfig()
                            ->toPrettyJson()
                    )
                    ->over()
                    ->await();
            })
            ->onReceive(function(Dialog $dialog) {
                return $dialog->goStage('start');
            });
    }


}