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
use gromver\models\ObjectModel;
use gromver\models\validators\MultipleValidator;
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
 * @property ObjectModel $model
 */
class MultipleField extends BaseField implements Arrayable
{
    use ArrayableTrait;

    private $_fieldConfig;

    public $required;
    public $multyfield;
    public $extra = 5;
    public $emptytext;

    const EXTRA_PREFIX = '__';

    public function __construct($config = [])
    {
        $config['field'] = $config['multyfield'];
        if (isset($config['default']) && is_array($config['default'])) {
            unset($config['default']);
        }

        $this->_fieldConfig = $config;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function link(BaseModel $model, $attribute)
    {
        parent::link($model, $attribute);

        $this->_value = new ArrayModel($this->model, $this->_fieldConfig);
        $this->_value->on(BaseModel::EVENT_FORM_NAME, [$this, 'formName']);

        return $this;
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
     * @inheritdoc
     */
    public function setValue($values)
    {
        // если $values имеет пустой значение, то принимаем его за пустой массив, в противном случае кастуем значение в массив
        $this->_value->setAttributes(empty($values) ? [] : (array)$values);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->toArray();
    }

    // Arrayable interface
    /**
     * @inheritdoc
     */
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
        $options = ArrayHelper::merge([
            'template' => "{before}\n<div class=\"grom-field-multiple-label clearfix\">{label}</div>\n{beginWrapper}\n{error}\n{input}\n{endWrapper}\n{hint}\n{after}",
            'parts' => [
                '{input}' => $this->renderEmptyText() . $this->renderFields($form) . $this->renderExtraFields($form) . $this->renderAppendButton(),
            ],
            'wrapperOptions' => [
                'class' => 'grom-field-multiple-container clearfix'
            ],
            /*'labelOptions' => [
                'class' => 'control-label'
            ]*/
        ], $options);

        return parent::field($form, $options);
    }

    /**
     * @param Yii\widgets\ActiveForm $form
     * @return string
     */
    protected function renderFields($form)
    {
        $fields = $this->_value->modelFields();
        $html = '';

        /** @var $field \gromver\models\fields\BaseField */
        foreach ($fields as $field) {
            $html .= strtr('<div class="grom-field-multiple-field">{remove}{down}{up}{field}</div>', [
                '{field}' => $field->field($form),
                '{up}' => Html::button('<i class="glyphicon glyphicon-chevron-up"></i>', ['class' => 'grom-field-multiple-up-btn']),
                '{down}' => Html::button('<i class="glyphicon glyphicon-chevron-down"></i>', ['class' => 'grom-field-multiple-down-btn']),
                '{remove}' => Html::button('<i class="glyphicon glyphicon-remove"></i>', ['class' => 'grom-field-multiple-close-btn'])
            ]);
        }

        return $html;
    }

    /**
     * @param Yii\widgets\ActiveForm $form
     * @return string
     */
    protected function renderExtraFields($form)
    {
        if ($this->extra <= 0) {
            return '';
        }

        $model = new ArrayModel($this->model, $this->_fieldConfig);
        $model->on(BaseModel::EVENT_FORM_NAME, [$this, 'prefixedFormName']);

        $extra = $this->extra;
        $index = count($this->_value);

        while ($extra--) {
            $model[$index++] = null;
        }

        $fields = $model->modelFields();
        $html = '';

        /** @var $field \gromver\models\fields\BaseField */
        foreach ($fields as $field) {
            $html .= strtr('<div class="grom-field-multiple-field grom-field-multiple-field_extra">{remove}{down}{up}{field}</div>', [
                '{field}' => $field->field($form),
                '{up}' => Html::button('<i class="glyphicon glyphicon-chevron-up"></i>', ['class' => 'grom-field-multiple-up-btn']),
                '{down}' => Html::button('<i class="glyphicon glyphicon-chevron-down"></i>', ['class' => 'grom-field-multiple-down-btn']),
                '{remove}' => Html::button('<i class="glyphicon glyphicon-remove"></i>', ['class' => 'grom-field-multiple-close-btn'])
            ]);
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function renderAppendButton()
    {
        return Html::button('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('gromver.models', 'Append') . '</span>', [
            'class' => 'btn btn-info grom-field-multiple-append-btn'
        ]);
    }

    /**
     * @return string
     */
    protected function renderEmptyText()
    {
        return Html::tag('div', $this->emptytext ? ($this->translation ? Yii::t($this->translation, $this->emptytext) : $this->emptytext) : '<em>' . Yii::t('gromver.models', 'Empty') . '</em>' . Html::hiddenInput(Html::getInputName($this->getModel(), $this->getAttribute())), ['class' => 'help-block grom-field-multiple-empty-text', 'style' => 'display: none;']);
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