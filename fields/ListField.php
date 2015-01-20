<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;


use dosamigos\selectize\Selectize;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class ListField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ListField extends BaseField
{
    public $multiple;
    public $items;
    public $editable;
    public $default;
    public $required;
    public $empty;

    public function init()
    {
        if (isset($this->default)) {
            $this->setValue($this->default);
        }

        if (!isset($this->items)) {
            throw new InvalidConfigException(Yii::t('gromver.models', __CLASS__ . '::items must be set for {attribute} attribute', ['attribute' => $this->getAttribute()]));
        }
    }

    /**
     * @param Yii\widgets\ActiveForm $form
     * @param array $options
     * @return Yii\widgets\ActiveField|static
     */
    public function field($form, $options = [])
    {
        return parent::field($form, $options)->widget(Selectize::className(), [
            'items' => $this->fetchItems(),
            'options' => [
                'disabled' => isset($this->disabled) ? 'disabled' : null,
                'multiple' => isset($this->multiple) ? 'multiple' : null,
            ],
            'clientOptions' => [
                'create' => isset($this->editable),
                'maxItems' => isset($this->multiple) ? 'NaN' : 1,
            ]
        ]);
    }

    private function fetchItems()
    {
        $items = is_array($this->items) ? $this->items : $this->invoke($this->items);

        if (isset($this->empty)) {
            $items = array_merge(['' => empty($this->empty) ? Yii::t('gromver.models', 'Select...') : $this->empty], $items);
        }

        if (isset($this->editable) && !isset($this->multiple) && ($value = $this->getValue()) && !array_key_exists($value, $items)) {
            $items[$value] = $value;
        }

        return $items;
    }

    /**
     * @param string $callable
     * @return mixed
     */
    public function invoke($callable)
    {
        if(strpos($callable, '::')) {
            return call_user_func($callable, $this);
        } else {
            return $this->getModel()->invoke($callable);
        }
    }
}