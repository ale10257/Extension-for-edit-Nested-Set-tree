<?php
namespace ale10257\ext;

use yii\web\AssetBundle;

class TreeAssets extends AssetBundle
{
    public $sourcePath = __DIR__  . '/assets';

    public $css = [
        'tree.css'
    ];

    public $js = [
        'tree.js'
    ];

    public $depends = [
        'yii\jui\JuiAsset',
        'rmrevin\yii\fontawesome\AssetBundle'
    ];

}
