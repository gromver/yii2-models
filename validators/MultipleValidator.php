<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\validators;


use Yii;

/**
 * Class MultipleValidator
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MultipleValidator extends \yii\validators\Validator
{
    /**
     * @var \gromver\models\ArrayModel
     */
    public $model;
    public $message;
    public $required = false;
    public $requiredMessage;


    /**
     * @param $value \gromver\models\fields\BaseField
     * @return array|null
     */
    protected function validateValue($value)
    {
        if ($this->required && !count($value->getValue())) {
            return [$this->requiredMessage ? $this->requiredMessage : Yii::t('gromver.models', 'Field {attribute} can\'t be empty.'), []];
        }

        if (!$this->model->validate()) {
            return [$this->message ? $this->message : Yii::t('gromver.models', 'Field {attribute} contains no valid items.'), []];
        }
    }
}