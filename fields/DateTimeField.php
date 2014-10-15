<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models\fields;

use kartik\widgets\DateTimePicker;

/**
 * Class DateTimeField
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class DateTimeField extends BaseField {
    public $default;
    public $required;
    public $format;// = 'd.m.Y H:i';


    /**
     * @inheritdoc
     */
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
        return parent::field($form, $options)->widget(DateTimePicker::className(), [
            'pluginOptions' => [
                'format' => $this->format
            ],
            'convertFormat' => true
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [$this->getAttribute(), 'date', 'format' => $this->format];

        if(isset($this->required))
            $rules[] = [$this->getAttribute(), 'required'];

        return $rules;
    }

    public function getValue()
    {
        return $this->__toString();
    }
}