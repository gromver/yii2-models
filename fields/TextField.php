<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;

/**
 * Class TextField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TextField extends BaseField {
    public $default;
    public $hint;
    public $required;
    public $length;
    public $max;
    public $min;
    public $pattern;
    public $email;


    public function init()
    {
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
        return parent::field($form, $options)->textInput();
    }

    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [$this->getAttribute(), 'string',
            'min' => strlen($this->min) ? (int)$this->min : null,
            'max' => strlen($this->max) ? (int)$this->max : null,
            'length' => strlen($this->length) ? (int)$this->length : null];

        if(isset($this->required))
            $rules[] = [$this->getAttribute(), 'required'];

        if(isset($this->pattern))
            $rules[] = [$this->getAttribute(), 'match', 'pattern' => $this->pattern];

        if(isset($this->email))
            $rules[] = [$this->getAttribute(), 'email'];

        return $rules;
    }

    public function getValue()
    {
        return $this->__toString();
    }
}