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
use Yii;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\InvalidParamException;
use yii\base\UnknownPropertyException;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class BaseField
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\models\BaseModel $model
 * @property string $attribute
 * @property mixed $value
 */
abstract class BaseField extends \yii\base\Object implements Arrayable
{
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
     * @var \gromver\models\BaseModel
     */
    private $_model;
    /**
     * @var string
     */
    private $_attribute;

    public static $builtInFields = [
        'text' => 'gromver\models\fields\TextField',
        'textarea' => 'gromver\models\fields\TextareaField',
        'list' => 'gromver\models\fields\ListField',
        'select' => 'gromver\models\fields\SelectField',
        'object' => 'gromver\models\fields\ObjectField',
        'multiple' => 'gromver\models\fields\MultipleField',
        'yesno' => ['class' => 'gromver\models\fields\ListField', 'items' => 'gromver\models\fields\BaseField::yesnoItems'],
        'datetime' => 'gromver\models\fields\DateTimeField',
        'modal' => 'gromver\models\fields\ModalField',
        'media' => 'gromver\models\fields\MediaField',
        'editor' => 'gromver\models\fields\EditorField'
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
                '{before}' => $this->before ? ($this->translation ? Yii::t($this->translation, $this->before) : $this->before) : null,
                '{after}' => $this->after ? ($this->translation ? Yii::t($this->translation, $this->after) : $this->after) : null
            ],
            'inputOptions' => [
                'disabled' => $this->disabled
            ],
            'labelOptions' => [
                'label' => $this->label()
            ],
        ], $options);

        $field = $form->field($this->model, $this->attribute, $options);

        return $field;
    }

    protected function label()
    {
        $label = isset($this->label) ? $this->label : Html::encode($this->model->getAttributeLabel($this->attribute));

        if ($this->translation) {
            $label = Yii::t($this->translation, $label);
        }

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
     * @return \gromver\models\ObjectModel
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

    static public function yesnoItems()
    {
        return [
            Yii::t('gromver.platform', 'No'),
            Yii::t('gromver.platform', 'Yes'),
        ];
    }
}