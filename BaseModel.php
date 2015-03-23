<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */


namespace gromver\models;

/**
 * Class BaseModel
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
abstract class BaseModel extends \yii\base\Model
{
    const EVENT_FORM_NAME = 'formName';
    const EVENT_INVOKE = 'invoke';

    public function formName()
    {
        $event = new FormNameEvent(['formName' => parent::formName()]);

        $this->trigger(self::EVENT_FORM_NAME, $event);

        return $event->formName;
    }

    public function invoke($funcName)
    {
        $event = new InvokeEvent(['funcName' => $funcName]);

        $this->trigger(self::EVENT_INVOKE, $event);

        return $event->result;
    }

    public function rules()
    {
        $rules = [];

        foreach($this->modelFields() as $field) {
            /** @var \gromver\models\fields\BaseField $field */
            $rules = array_merge($rules, $field->rules());
        }

        return $rules;
    }


    public function scenarios()
    {
        $attributes = [];

        foreach ($this->modelFields() as $attribute => $field)
        {
            /** @var $field \gromver\models\fields\BaseField */
            if (isset($field->disabled))
                continue;

            if (isset($field->access)) {
                $rules = preg_split('/\s+/', $field->access, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($rules as $rule) {
                    if (!\Yii::$app->user->can($rule))
                        continue 2;
                }
            }

            $attributes[] = $attribute;
        }

        return [self::SCENARIO_DEFAULT => $attributes];
    }

    public function modelFields()
    {
        return [];
    }
} 