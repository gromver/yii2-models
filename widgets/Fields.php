<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\widgets;


use gromver\models\BaseModel;
use gromver\models\ModelFormAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class Fields
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
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
            throw new InvalidConfigException(__CLASS__ . '::model must be an instance of \gromver\models\BaseModel.');
        }
    }

    public function run()
    {
        $fields = $this->model->modelFields();

       $form = ActiveForm::begin(ArrayHelper::merge([
            'layout' => 'horizontal'
        ], $this->formOptions));

        /** @var $field \gromver\models\fields\BaseField */
        foreach ($fields as $field) {
            echo $field->field($form, $form->fieldConfig);
        }

        ActiveForm::end();

        $this->getView()->registerAssetBundle(ModelFormAsset::className());
    }
} 