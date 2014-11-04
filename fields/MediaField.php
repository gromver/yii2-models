<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models\fields;

use mihaildev\elfinder\InputFile;
use Yii;

/**
 * Class MediaField
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class MediaField extends BaseField {
    public $default;
    public $required;
    public $controller;


    public function init()
    {
        if (isset($this->default)) {
            $this->setValue($this->default);
        }
    }

    /**
     * @param \Yii\widgets\ActiveForm $form
     * @param array $options
     * @return \Yii\widgets\ActiveField|static
     */
    public function field($form, $options = [])
    {
        return parent::field($form, $options)->widget(InputFile::className(), [
            'language'      => Yii::$app->language,
            'controller'    => $this->controller, // вставляем название контроллера, по умолчанию равен elfinder
            'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
            'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options'       => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple'      => false       // возможность выбора нескольких файлов
        ]);
    }

    public function rules()
    {
        $rules = parent::rules();

        if(isset($this->required))
            $rules[] = [$this->getAttribute(), 'required'];

        return $rules;
    }

    public function getValue()
    {
        return $this->__toString();
    }
}