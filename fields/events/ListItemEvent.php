<?php
/**
 * @link https://github.com/gromver/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace gromver\models\fields\events;

/**
 * Class ListItemEvent
 * @package yii2-models
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender \gromver\models\fields\ListField
 */
class ListItemEvent extends \yii\base\Event {
    /**
     * @var array fo list items
     */
    public $items;
    /**
     * @var \gromver\models\ObjectModel
     */
    public $model;
} 