<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models;


/**
 * Class InvokeEvent
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class InvokeEvent extends \yii\base\Event
{
    public $funcName;
    public $result;
} 