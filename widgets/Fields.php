<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models\widgets;


use menst\models\BaseModel;
use menst\models\ModelFormAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class Fields
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class Fields extends Widget
{
    /**
     * @var BaseModel
     */
    public $model;
    public $formOptions = [];

    public function init()
    {
        if (!$this->model instanceof BaseModel) {
            throw new InvalidConfigException(__CLASS__ . '::model must be an instance of \menst\models\BaseModel.');
        }
    }

    public function run()
    {
        $fields = $this->model->modelFields();

       $form = ActiveForm::begin(ArrayHelper::merge([
            'layout' => 'horizontal'
        ], $this->formOptions));

        /** @var $field \menst\models\fields\BaseField */
        foreach ($fields as $field) {
            echo $field->field($form, $form->fieldConfig);
        }

        ActiveForm::end();

        $this->getView()->registerAssetBundle(ModelFormAsset::className());
    }
} 