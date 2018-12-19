<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'api' => [
            'class' => 'common\components\Api',
        ],
        'view' => [
        'renderers' => [
            'twig' => [
                'class' => 'yii\twig\ViewRenderer',
                // set cachePath to false in order to disable template caching
                'cachePath' => false, //'@runtime/Twig/cache',
                // Array of twig options:
                'options' => [
                    'auto_reload' => true,
                ],
                // add Yii helpers or widgets here
                'globals' => [
                    'html' => '\yii\helpers\Html',
                ]
            ]
        ]
      ]
    ],
];
