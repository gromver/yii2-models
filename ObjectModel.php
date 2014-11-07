<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\helpers\StringHelper;

/**
 * Class Model
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * Моделька основанная на публичных полях данного объекта
 */
class ObjectModel extends DynamicModel {
    private static $_specifications = [];
    private static $_callStack = [];
    private static $_callContext = [];

    private $_source;
    private $_sourceClass;

    /**
     * @param string|object $source
     * @param array $config
     * @throws \yii\base\InvalidParamException
     * @internal param array $attributes
     */
    public function __construct($source, $config = [])
    {
        if (is_string($source)) {
            $this->_sourceClass = $source;
        }
        elseif (is_object($source)) {
            $this->_source = $source;
            $this->_sourceClass = get_class($source);
        } else {
            throw new InvalidParamException('$source должен быть объектом или именем класса объекта.');
        }

        Object::__construct($config);
    }

    public function init()
    {
        //Проверка на зацикленность полей модели
        $beginner = !count(self::$_callStack);
        self::$_callContext[] = $this->_sourceClass;
        self::$_callStack[md5(json_encode(self::$_callContext))] = $this->_sourceClass;
        if (count(array_keys(self::$_callStack, $this->_sourceClass)) > 1) {
            throw new InvalidConfigException('Обнаружен бесконечный цикл.');
        }

        //инициализация
        $specification = $this->getClassSpecification($this->_sourceClass);

        if ($this->_source instanceof SpecificationInterface) {
            $this->_source->processSpecification($specification);
        }

        foreach ($specification as $attribute => $settings) {
            //своиства класса с анотацией @ignore игнорируются))
            if (isset($settings['ignore']))
                continue;

            //определяем атрибут для модели
            $this->defineAttribute($attribute, $settings);

            //если в качестве структуры дан обьект, то инициализируем модель значениями объекта
            if (isset($this->_source)) {
                $this->{$attribute} = $this->_source->{$attribute};
            }
        }

        //очищаем текущий контекст
        array_pop(self::$_callContext);
        //очищаем стек, для проверки бесконечного цикла, если он был начат данной моделью
        if ($beginner) {
            self::$_callStack = [];
        }
    }

    public function formName()
    {
        $event = new FormNameEvent(['formName' => StringHelper::basename($this->_sourceClass)]);

        $this->trigger(self::EVENT_FORM_NAME, $event);

        return $event->formName;
    }

    public function getSource()
    {
        return isset($this->_source) ? $this->_source : \Yii::createObject($this->_sourceClass);
    }

    public function getSourceClass()
    {
        return $this->_sourceClass;
    }

    public static function getClassSpecification($className)
    {
        if(!isset(self::$_specifications[$className]))
        {
            $specification = [];
            $reflection = new \ReflectionClass($className);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            $defaultProperties = $reflection->getDefaultProperties();
            foreach($properties as $property)
            {
                if($property->isStatic()) continue;

                $settings = self::processProperty($property);

                if(!isset($settings['default']))
                    $settings['default'] = $defaultProperties[$property->getName()];

                $specification[$property->getName()] = $settings;
            }

            self::$_specifications[$className] = $specification;
        }

        return self::$_specifications[$className];
    }

    /**
     * @param $property \ReflectionProperty
     * @return array
     */
    private static function processProperty($property)
    {
        $comment=$property->getDocComment();

        $comment=strtr($comment,array("\r\n"=>"\n","\r"=>"\n")); // make line endings consistent: win -> unix, mac -> unix
        $comment=preg_replace('/^\s*\**(\s*?$|\s*)/m','',$comment);

        $settings = [];

        if($n=preg_match_all('/^@(\w+)(.*)$/im',$comment,$matches))
        {
            for($i=0;$i<$n;++$i)
            {
                $attrName = strtolower($matches[1][$i]);
                $attrValue = trim($matches[2][$i]);
                $settings[$attrName] = $attrValue;
            }
        }

        return $settings;
    }

    /**
     * хелпер - для запуска функций принадлежащих self::sourceClass, используется статический контекст
     * в качестве параметра в функцию передается модель
     * @param $funcName string
     * @return mixed
     */
    public function invoke($funcName)
    {
        return call_user_func([$this->_sourceClass, $funcName], $this);
    }

    public function attributeLabels()
    {
        return array_map(function($field) {
            /** @var $field \gromver\models\fields\BaseField */
            return $field->label ? $field->label : $this->generateAttributeLabel($field->attribute);
        }, $this->modelFields());
    }
}