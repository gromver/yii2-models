<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 26.03.16
 * Time: 9:26
 */

namespace gromver\models\widgets;


use mihaildev\elfinder\InputFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class MediaInput extends InputWidget {
    public $template = '{input}{img}';
    public $fileInputOptions = [];

    public function run()
    {
        $blockId = 'block_' . $this->id;
        $this->view->beginBlock($blockId);

        $inputFile = InputFile::begin(ArrayHelper::merge([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'name' => $this->name,
            'value' => $this->value
        ], $this->fileInputOptions));

        $inputId = $inputFile->options['id'];
        $previewId = 'media-' . $inputId;

        InputFile::end();

        $this->view->endBlock();

        $replace['{input}'] = $this->view->blocks[$blockId];
        $replace['{img}'] = Html::tag('div', '', [
            'class' => 'form-media-preview',
            'id' => $previewId
        ]);

        echo strtr($this->template, $replace);


        $this->view->registerJs(
<<<JS
    $('#$inputId').change(function() {
        var src = $(this).val();
        if (src != '') {
            var img = $('<img/>').attr('src', src);
            $('#$previewId').wrapInner(img);
        } else {
            $('#$previewId').empty();
        }
    }).change();
JS
        );
    }
} 