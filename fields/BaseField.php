<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models\fields;

use menst\models\BaseModel;
use Yii;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\base\UnknownPropertyException;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class BaseField
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 *
 * @property \menst\models\BaseModel $model
 * @property string $attribute
 * @property mixed $value
 */
abstract class BaseField extends Object implements Arrayable {
    use ArrayableTrait;

    public $type;
    public $disabled;
    public $before;
    public $after;
    public $label;
    public $hint;
    public $translation;

    /**
     * @var mixed
     */
    protected $_value;
    /**
     * @var \menst\models\BaseModel
     */
    private $_model;
    /**
     * @var string
     */
    private $_attribute;

    public static $builtInFields = [
        'text' => 'menst\models\fields\TextField',
        'textarea' => 'menst\models\fields\TextareaField',
        'list' => 'menst\models\fields\ListField',
        'select' => 'menst\models\fields\SelectField',
        'object' => 'menst\models\fields\ObjectField',
        'multiple' => 'menst\models\fields\MultipleField',
        'yesno' => ['class' => 'menst\models\fields\ListField', 'items' => ['Нет', 'Да']],
        'datetime' => 'menst\models\fields\DateTimeField',
        'modal' => 'menst\models\fields\ModalField',
        'media' => 'menst\models\fields\MediaField',
        'editor' => 'menst\models\fields\EditorField'
    ];


    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {}
    }

    /**
     * @param static|array|string $config объект BaseField, имя класса, конфигурационный массив
     * @return static
     */
    public static function createField($config)
    {
        if (is_string($config)) {
            $instance = Yii::createObject(['class' => $config]);
        } elseif (is_array($config)) {
            //если в конфиге не указан клас поля, то определяем его по типу
            if (!isset($config['class'])) {
                //если не указан тип то используется по умолчанию 'text'
                $type = ArrayHelper::remove($config, 'type', 'text');
                $typeConfig = self::$builtInFields[$type];
                if (is_array($typeConfig)) {
                    //если конфигурация типа задана массивом то мерджим с текущей конфигурацией
                    $config = array_merge($typeConfig, $config);
                } else {
                    //если строка то подставляем ее в качестве имени класса в конфигурацию
                    $config['class'] = $typeConfig;
                }
            }
            $instance = Yii::createObject($config);
        } else {
            //предполагается что дан объект BaseField
            $instance = $config;
        }

        if (!$instance instanceof self) {
            throw new InvalidParamException(__CLASS__ . ' object was not created.');
        }

        return $instance;
    }

    /**
     * @param $value
     * @return static
     */
    public function setValue($value)
    {
        $this->_value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (is_array($this->_value)) {
            return 'array(...)';
        }

        if (is_object($this->_value)) {
            return '#Object: ' . get_class($this->_value);
        }

        return (string)$this->_value;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @param $form Yii\widgets\ActiveForm
     * @param array $options
     * @return Yii\widgets\ActiveField
     */
    public function field($form, $options = [])
    {
        $options = ArrayHelper::merge([
            'template' => "{before}\n{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}\n{after}",
            'parts' => [
                '{before}' => $this->before,
                '{after}' => $this->after
            ],
            'inputOptions' => [
                'disabled' => $this->disabled
            ],
            'labelOptions' => [
                'label' => $this->label()
            ]
        ], $options);

        $field = $form->field($this->model, $this->attribute, $options);

        return $field;
    }

    protected function label()
    {
        $label = isset($this->label) ? (isset($this->translation) ? Yii::t($this->translation, $this->label) : $this->label) : Html::encode($this->model->getAttributeLabel($this->attribute));

        if ($this->hint) {
            $hintId = Html::getInputId($this->model, $this->attribute) . '-hint';
            Yii::$app->getView()->registerAssetBundle(BootstrapPluginAsset::className());
            Yii::$app->getView()->registerJs('jQuery("#' . $hintId . '").tooltip()');
            $label .= Html::tag('span', ' <i class="glyphicon glyphicon-question-sign"></i>', [
                'id' => $hintId,
                'title' => $this->hint,
                'data-toggle' => 'tooltip',
            ]);
        }

        return $label;
    }

    /**
     * @return \menst\models\ObjectModel
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * @param BaseModel $model
     * @param $attribute
     * @return $this
     */
    public function link(BaseModel $model, $attribute)
    {
        $this->_model = $model;
        $this->_attribute = $attribute;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return $this->_value;
    }
}