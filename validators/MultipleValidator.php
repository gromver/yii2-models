<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models\validators;

use yii\validators\Validator;
use Yii;

/**
 * Class MultipleValidator
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class MultipleValidator extends Validator {
    /**
     * @var \menst\models\ArrayModel
     */
    public $model;
    public $message;
    public $required = false;
    public $requiredMessage;


    /**
     * @param $value \menst\models\fields\BaseField
     * @return array|null
     */
    protected function validateValue($value)
    {
        if ($this->required && !count($value->getValue())) {
            return [$this->requiredMessage ? $this->requiredMessage : Yii::t('menst.models', 'Field {attribute} can\'t be empty.'), []];
        }

        if (!$this->model->validate()) {
            return [$this->message ? $this->message : Yii::t('menst.models', 'Field {attribute} contains no valid items.'), []];
        }
    }
}