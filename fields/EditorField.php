<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

/**
 * Class EditorField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class EditorField extends BaseField {
    public $default;
    public $required;
    public $controller;
    public $editorOptions = [];


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
        $options = array_merge([
            'labelOptions' => ['class' => 'col-sm-12'],
            'wrapperOptions' => ['class' => 'col-sm-12']
        ], $options);

        return parent::field($form, $options)->widget(CKEditor::className(), [
            'editorOptions' => ElFinder::ckeditorOptions($this->controller, $this->editorOptions)
        ]);
    }

    public function rules()
    {
        $rules = parent::rules();

        if (isset($this->required)) {
            $rules[] = [$this->getAttribute(), 'required'];
        }

        return $rules;
    }

    public function getValue()
    {
        return $this->__toString();
    }
}