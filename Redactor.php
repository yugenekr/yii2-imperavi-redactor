<?php
/**
 * @link      https://github.com/SDKiller/yii2-imperavi-redactor
 * @package   zyx\yii2-imperavi-redactor
 * @copyright Copyright (c) 2014 Serge Postrash, Copyright (c) 2010-2014 by YiiExt contributors
 * @license   BSD 3-Clause, see LICENSE.md
 */

namespace zyx\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;


/**
 * Requires Imperavi Redactor WYSIWYG editor version 10.0.*
 */

class Redactor extends InputWidget
{
    /**
     * @var array Redactor settings
     * @see http://imperavi.com/redactor/docs/settings/
     */
    public $clientOptions = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerAssets();
    }

    /**
     * Register asset bundle and attach redactor to html tag
     */
    public function registerAssets()
    {
        $view = $this->getView();

        $bundle = RedactorAsset::register($view);

        $lang = empty($this->clientOptions['lang']) ? Yii::$app->language : $this->clientOptions['lang'];
        $lang = substr($lang, 0, 2);
        if ($lang !== 'en' && is_file(Yii::getAlias($bundle->basePath, false) . '/lang/' . $lang . '.js')) {
            $view->registerJsFile($bundle->baseUrl . '/lang/' . $lang . '.js', ['depends' => [$bundle::className()]]);
        }

        $plugins = [
            'clips',
            'counter',
            'definedlinks',
            'filemanager',
            'fontcolor',
            'fontfamily',
            'fontsize',
            'fullscreen',
            'imagemanager',
            'limiter',
            'table',
            'textdirection',
            'textexpander',
            'video'
        ];

        if (isset($this->clientOptions['plugins']) && is_array($this->clientOptions['plugins'])) {
            foreach ($this->clientOptions['plugins'] as $plugin) {
                if (in_array($plugin, $plugins, true)) {
                    $view->registerJsFile($bundle->baseUrl . '/plugins/' . $plugin . '/' . $plugin . '.js', ['depends' => [$bundle::className()]]);
                    if ($plugin == 'clips') {
                        $view->registerCssFile($bundle->baseUrl . '/plugins/' . $plugin . '/' . $plugin . '.css', ['depends' => [$bundle::className()]]);
                    }
                }
            }
        }

        $clientOptions = Json::encode($this->clientOptions);

        $id = isset($this->options['id']) ? $this->options['id'] : $this->id;
        $js = 'jQuery(\'#' . $id . '\').redactor(' . $clientOptions . ');';

        $view->registerJs($js);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
    }

}
