<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;

use gromver\models\BaseModel;
use gromver\models\ObjectModel;
use gromver\models\validators\ObjectValidator;
use gromver\models\widgets\Fields;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class ObjectField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property BaseModel $_value
 */
class ObjectField extends BaseField
{
    public $object;

    public function init()
    {
        if (!isset($this->object)) {
            throw new InvalidConfigException(Yii::t('gromver.models', __CLASS__ . '::object must be set for {attribute} attribute', ['attribute' => $this->getAttribute()]));
        }

        $this->_value = new ObjectModel($this->object);
        $this->_value->on(BaseModel::EVENT_FORM_NAME, [$this, 'formName']);

        parent::init();
    }

    /**
     * @param $event \gromver\models\FormNameEvent
     */
    public function formName($event)
    {
        $event->formName = Html::getInputName($this->model, $this->attribute);
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->_value->setAttributes($value);

        return $this;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->toArray();
    }
    /**
     * @inheritdoc
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return $this->_value->toArray($fields, $expand, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [$this->getAttribute(), ObjectValidator::className(), 'model' => $this->_value];

        return $rules;
    }

    /**
     * @param Yii\widgets\ActiveForm $form
     * @param array $options
     * @return Yii\widgets\ActiveField
     */
    public function field($form, $options = [])
    {
        $options = ArrayHelper::merge([
            'template' => "{before}\n{label}\n{beginWrapper}\n{error}\n{input}\n{endWrapper}\n{hint}\n{after}",
            'parts' => [
                '{label}' => Html::tag('h2', Html::encode($this->getModel()->getAttributeLabel($this->getAttribute()))),
                '{input}' => Fields::widget(['model' => $this->_value]),
            ],
            'wrapperOptions' => [
                'class' => 'well'
            ]
        ], $options);

        return parent::field($form, $options);
    }
}