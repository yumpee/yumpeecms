<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css','css/jquery.dataTables.min.css',
    ];
    public $js = [
        'js/tinymce/tinymce.min.js','js/tinymce/jquery.tinymce.min','js/datatables/jquery.dataTables.min.js','js/ace-builds-master/src-noconflict/ace.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
