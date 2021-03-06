<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;


use gromver\widgets\ModalIFrame;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class ModalField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ModalField extends BaseField
{
    public $default;
    public $required;
    public $url;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if(isset($this->default))
            $this->setValue($this->default);
    }

    /**
     * @param \Yii\widgets\ActiveForm $form
     * @param array $options
     * @return \Yii\widgets\ActiveField|static
     */
    public function field($form, $options = [])
    {
        $options = ArrayHelper::merge([
            'template' => "{before}\n{label}\n{beginWrapper}\n<div class=\"input-group\">{input}{controls}</div>\n{error}\n{endWrapper}\n{hint}\n{after}",
            'parts' => [
                '{controls}' => $this->modalButton()
            ]
        ], $options);


        return parent::field($form, $options)->textInput();
    }

    protected function modalButton()
    {
        $inputId = Html::getInputId($this->getModel(), $this->getAttribute());

        return ModalIFrame::widget([
            'options' => [
                'class' => 'input-group-btn'
            ],
            'label' => Html::tag('span', '<i class="glyphicon glyphicon-folder-open"></i>', ['class'=>'btn btn-default']),
            'url' => $this->url,
            'dataHandler' => "function(data){
                    $('#{$inputId}').val(data.value)
                }"
        ]);
    }

    /**
     * @inheritdoc
     */
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