<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models;

use gromver\models\fields\BaseField;
use yii\base\ArrayAccessTrait;
use yii\base\Object;
use yii\validators\Validator;

/**
 * Class MultipleFieldModel
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ArrayModel extends BaseModel implements \Countable
{
    use ArrayAccessTrait;
    /**
     * @var array
     */
    private $_fieldConfig;
    /**
     * @var \gromver\models\fields\BaseField[]
     */
    private $data = [];

    /**
     * @param array $fieldConfig
     * @param array $config
     * @internal param object|string $object
     * @internal param array $attributes
     */
    public function __construct(array $fieldConfig, $config = [])
    {
        $this->_fieldConfig = $fieldConfig;

        Object::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name]->getValue();
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name]->setValue($value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->data)) {
            return isset($this->data[$name]);
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->data)) {
            unset($this->data[$name]);
        } else {
            parent::__unset($name);
        }
    }

    /**
     * Adds a validation rule to this model.
     * You can also directly manipulate [[validators]] to add or remove validation rules.
     * This method provides a shortcut.
     * @param string|array $attributes the attribute(s) to be validated by the rule
     * @param mixed $validator the validator for the rule.This can be a built-in validator name,
     * a method name of the model class, an anonymous function, or a validator class name.
     * @param array $options the options (name-value pairs) to be applied to the validator
     * @return static the model itself
     */
    public function addRule($attributes, $validator, $options = [])
    {
        $validators = $this->getValidators();
        $validators->append(Validator::createValidator($validator, $this, (array) $attributes, $options));

        return $this;
    }

    public function setAttributes($values, $safeOnly = true)
    {
        $this->data = [];
        $this->getValidators()->exchangeArray([]);

        if (is_array($values)) {
            foreach ($values as $value) {
                $this[] = $value;
            }
        }
    }

    /**
     * Пляски с бубнами для того чтоб валидация нармально отрабатывала атрибуты с числовыми именами
     * @inheritdoc
     */
    public function attributes()
    {
        return array_map(function($value){return (string)$value;}, array_keys($this->data));
    }

    /**
     * Пляски с бубнами для того чтоб валидация нармально отрабатывала атрибуты с числовыми именами
     * @inheritdoc
     */
    public function activeAttributes()
    {
        return array_map(function($value){return (string)$value;}, parent::activeAttributes());
    }

    public function modelFields()
    {
        return $this->data;
    }

    /**
     * @return BaseField
     */
    public function createField()
    {
        return BaseField::createField($this->_fieldConfig);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset]->getValue() : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $item)
    {
        if ($offset == null) {
            $offset = $this->count();
        }

        if ($this->offsetExists($offset)) {
            $this->data[$offset]->setValue($item);
        } else {
            $field = BaseField::createField($this->_fieldConfig)->link($this, $offset);
            if ($item !== null) $field->setValue($item);

            $this->data[$offset] = $field;

            $rules = $field->rules();

            foreach($rules as $rule) {
                $this->addRule((string)$rule[0], $rule[1], array_slice($rule, 2));
            }
        }
    }
}