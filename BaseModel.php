<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models;

use yii\base\Model;

/**
 * Class BaseModel
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
abstract class BaseModel extends Model
{
    const EVENT_FORM_NAME = 'formName';

    public function formName()
    {
        $event = new FormNameEvent(['formName' => parent::formName()]);

        $this->trigger(self::EVENT_FORM_NAME, $event);

        return $event->formName;
    }

    public function rules()
    {
        $rules = [];

        foreach($this->modelFields() as $field) {
            /** @var \menst\models\fields\BaseField $field */
            $rules = array_merge($rules, $field->rules());
        }

        return $rules;
    }


    public function scenarios()
    {
        $attributes = [];

        foreach($this->modelFields() as $attribute => $field)
        {
            if(!isset($field->disabled)) $attributes[] = $attribute;
        }

        return [self::SCENARIO_DEFAULT => $attributes];
    }

    public function modelFields()
    {
        return [];
    }
} 