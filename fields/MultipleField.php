<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields;

use gromver\models\ArrayModel;
use gromver\models\BaseModel;
use gromver\models\validators\MultipleValidator;
use gromver\models\widgets\Fields;
use Yii;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class MultipleField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property ArrayModel $_value
 */
class MultipleField extends BaseField implements Arrayable {
    use ArrayableTrait;

    private $_fieldConfig;

    public $required;
    public $fieldtype;
    public $extra = 5;
    public $emptytext;

    const EXTRA_PREFIX = '__';

    public function __construct($config)
    {
        $config['type'] = $config['fieldtype'];
        if (isset($config['default']) && is_array($config['default'])) {
            unset($config['default']);
        }

        $this->_fieldConfig = $config;

        parent::__construct($config);
    }

    public function init()
    {
        $this->_value = new ArrayModel($this->_fieldConfig);
        $this->_value->on(BaseModel::EVENT_FORM_NAME, [$this, 'formName']);
        $this->_value->on(BaseModel::EVENT_INVOKE, [$this, 'invoke']);

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
     * @param $event \gromver\models\FormNameEvent
     */
    public function prefixedFormName($event)
    {
        $event->formName = self::EXTRA_PREFIX . Html::getInputName($this->model, $this->attribute);
    }

    /**
     * @param $event \gromver\models\InvokeEvent
     */
    public function invoke($event)
    {
        $event->result = $this->model->invoke($event->funcName);
    }

    public function setValue($values)
    {
        // если $values имеет пустой значение, то принимаем его за пустой массив, в противном случае кастуем значение в массив
        $this->_value->setAttributes(empty($values) ? [] : (array)$values);

        return $this;
    }

    public function getValue()
    {
        return $this->toArray();
    }

    //Arrayable interface
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return $this->_value->toArray($fields, $expand, $recursive);
    }

    /**
     * @param Yii\widgets\ActiveForm $form
     * @param array $options
     * @return Yii\widgets\ActiveField
     */
    public function field($form, $options = [])
    {
        if ($this->fieldtype == 'object') {
            $options = ArrayHelper::merge([
                'template' => "{before}\n{label}\n{beginWrapper}\n{error}\n{input}\n{endWrapper}\n{hint}\n{after}",
                'wrapperOptions' => [
                    'class' => null
                ],
                'labelOptions' => [
                    'class' => 'h2'
                ]
            ], $options);
        } else {
            $options = ArrayHelper::merge([
                'template' => "{before}\n{label}\n{beginWrapper}\n{error}\n{input}\n{endWrapper}\n{hint}\n{after}"
            ], $options);
        }

        $options['parts']['{input}'] = Html::tag('div', $this->renderEmptyText() . $this->renderFields() . $this->renderExtraFields(), ['class' => 'multyfield-container']);

        return parent::field($form, $options);
    }

    /**
     * @return string
     */
    protected function renderFields()
    {
        return Fields::widget([
            'model' => $this->_value,
            'formOptions' => [
                'fieldConfig'=>[
                    'template' => "{before}\n{remove}\n{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}\n{after}",
                    'parts' => [
                        '{label}' => '',
                        '{remove}' => Html::button('&times;', ['class' => 'close multyfield-close-btn'])
                    ],
                    'wrapperOptions' => [
                        'class' => 'col-sm-12' . ($this->fieldtype == 'object' ? ' well' : ' row')
                    ]
                ],
                'options' => [
                    'class' => 'multyfield-fields'
                ]
            ]
        ]);
    }

    /**
     * @return string
     */
    protected function renderExtraFields()
    {
        if ($this->extra <= 0) {
            return '';
        }

        $model = new ArrayModel($this->_fieldConfig);
        $model->on(BaseModel::EVENT_FORM_NAME, [$this, 'prefixedFormName']);
        $model->on(BaseModel::EVENT_INVOKE, [$this, 'invoke']);

        $extra = $this->extra;
        $index = count($this->_value);

        while ($extra--) {
            $model[$index++] = null;
        }

        return Fields::widget([
            'model' => $model,
            'formOptions' => [
                'fieldConfig'=>[
                    'template' => "{before}\n{remove}\n{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}\n{after}",
                    'parts' => [
                        '{label}' => '',
                        '{remove}' => Html::button('&times;', ['class' => 'close multyfield-close-btn'])
                    ],
                    'wrapperOptions' => [
                        'class' => 'col-sm-12' . ($this->fieldtype == 'object' ? ' well' : ' row')
                    ],
                    'options' => [
                        'class' => 'form-group hidden'
                    ]
                ],
                'options' => [
                    'class' => 'multyfield-extra-fields'
                ]
            ]
        ]) . $this->renderAppendButton();
    }

    /**
     * @return string
     */
    protected function renderAppendButton()
    {
        return Html::button('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('gromver.models', 'Append') . '</span>', [
            'class' => 'btn btn-info multyfield-append-btn'
        ]);
    }

    /**
     * @return string
     */
    protected function renderEmptyText()
    {
        return Html::tag('div', $this->emptytext ? ($this->translation ? Yii::t($this->translation, $this->emptytext) : $this->emptytext) : '<em>' . Yii::t('gromver.models', 'Empty') . '</em>' . Html::hiddenInput(Html::getInputName($this->getModel(), $this->getAttribute())), ['class' => 'help-block multyfield-empty-text', 'style' => 'display: none;']);
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [$this->getAttribute(), MultipleValidator::className(), 'required' => !!$this->required, 'model' => $this->_value];

        return $rules;
    }
} 