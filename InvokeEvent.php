<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models;


use yii\base\Event;

/**
 * Class InvokeEvent
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class InvokeEvent extends Event {
    public $funcName;
    public $result;
} 