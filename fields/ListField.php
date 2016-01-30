<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;


use gromver\models\fields\events\ListItemsEvent;
use gromver\models\ObjectModelInterface;
use kartik\select2\Select2;
use yii\base\Event;
use yii\base\InvalidConfigException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class ListField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ListField extends BaseField
{
    const EVENT_FETCH_ITEMS = 'fetchItems';

    public $multiple;
    public $items;
    public $editable;
    public $default;
    public $required;
    public $empty;

    public function init()
    {
        parent::init();

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
        return parent::field($form, $options)->widget(Select2::className(), [
            'data' => $this->fetchItems(),
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => [
                'disabled' => isset($this->disabled) ? 'disabled' : null,
                'multiple' => isset($this->multiple) ? 'multiple' : null,
            ],
            'pluginOptions' => [
                'create' => isset($this->editable),
                'maxItems' => isset($this->multiple) ? 'NaN' : 1,
            ]
        ]);
    }

    private function fetchItems()
    {
        $items = is_array($this->items) ? $this->items : $this->invoke($this->items);

        if (isset($this->empty)) {
            $items = ArrayHelper::merge(['' => empty($this->empty) ? Yii::t('gromver.models', 'Select...') : $this->empty], $items);
        }

        if (isset($this->editable) && !isset($this->multiple) && ($value = $this->getValue()) && !array_key_exists($value, $items)) {
            $items[$value] = $value;
        }

        if ($this->getModel() instanceof ObjectModelInterface) {
            preg_match_all('/\[([a-zA-Z]\w*)\]/', Html::getInputName($this->getModel(), $this->getAttribute()), $matches);

            $event = new ListItemsEvent([
                'items' => $items,
                'model' => $this->getModel()->getObjectModel(),
                'attribute' => end($matches[1])
            ]);

            Event::trigger(static::className(), self::EVENT_FETCH_ITEMS, $event);

            return $event->items;
        }

        return $items;
    }

    /**
     * @param string $callable
     * @throws InvalidConfigException
     * @return array
     */
    public function invoke($callable)
    {
        if(strpos($callable, '::')) {
            return call_user_func($callable, $this);
        } else {
            if ($this->getModel() instanceof ObjectModelInterface) {
                return $this->getModel()->getObjectModel()->invoke($callable);
            } else {
                throw new InvalidConfigException('Unable to fetch items.');
            }
        }
    }

    static public function itemsYesno()
    {
        return [
            Yii::t('gromver.models', 'No'),
            Yii::t('gromver.models', 'Yes'),
        ];
    }
}