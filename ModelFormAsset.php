<?php
/**
 * @link https://github.com/menst/yii2-models.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-models/blob/master/LICENSE
 * @package yii2-models
 * @version 1.0.0
 */

namespace menst\models;

use yii\web\AssetBundle;

/**
 * Class ModelFormAsset
 * @package yii2-models
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class ModelFormAsset extends AssetBundle {
    public $sourcePath = '@menst/models/assets';

    public $js = [
        'js/structure.js'
    ];

    public $css = [
        'css/structure.css'
    ];
} 