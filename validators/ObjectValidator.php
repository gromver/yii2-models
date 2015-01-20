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
 * Class ObjectValidator
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ObjectValidator extends \yii\validators\Validator
{
    public $message;
    /**
     * @var \gromver\models\ObjectModel
     */
    public $model;

    /**
     * @param $value \gromver\models\fields\BaseField
     * @return array|null
     */
    protected function validateValue($value)
    {
        if (!$this->model->validate()) {
            return [$this->message ? $this->message : Yii::t('gromver.models', 'Field {attribute} contains no valid items.'), []];
        }
    }
}