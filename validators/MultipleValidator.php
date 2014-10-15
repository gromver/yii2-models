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

/**
 * Class MultipleValidator
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class MultipleValidator extends Validator {
    /**
     * @var \menst\models\MultipleFieldModel
     */
    public $structure;

    public $message = 'Поле {attribute} имеет не валидные элементы.';

    public $required = false;
    public $requiredMessage = 'Поле {attribute} не может быть пустым.';


    /**
     * @param $value \menst\models\fields\BaseField
     * @return array|null
     */
    protected function validateValue($value)
    {
        if ($this->required && !count($value->getValue()))
            return [$this->requiredMessage, []];

        if (!$this->structure->validate()) {
            return [$this->message, []];
        }
    }
}