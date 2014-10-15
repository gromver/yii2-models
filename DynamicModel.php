<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models;

use menst\models\fields\BaseField;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Object;

/**
 * Class Model
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 *
 * Динамическая моделька основанная на умных полях
 */
class DynamicModel extends BaseModel {
    /**
     * @var \menst\models\fields\BaseField[]
     */
    private $_attributes;
    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name]->getValue();
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_attributes)) {
            $this->_attributes[$name]->setValue($value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return isset($this->_attributes[$name]);
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            unset($this->_attributes[$name]);
        } else {
            parent::__unset($name);
        }
    }

    /**
     * Defines an attribute.
     * @param string $name the attribute name
     * @param BaseField|string|array $field the attribute value
     */
    public function defineAttribute($name, $field)
    {
        $field = BaseField::createField($field);

        $field->link($this, $name);

        $this->_attributes[$name] = $field;
    }

    /**
     * Undefines an attribute.
     * @param string $name the attribute name
     */
    public function undefineAttribute($name)
    {
        unset($this->_attributes[$name]);
    }

    public function attributes()
    {
        return array_keys($this->_attributes);
    }

    public function modelFields()
    {
        return $this->_attributes;
    }
}