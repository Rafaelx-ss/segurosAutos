<?php
$params = require __DIR__ . '/params.php';
$timesess = require(__DIR__ . '/session.php');

$config = [
    'id' => 'basic',
    'language' => 'es',
    'timeZone' => 'America/Mexico_City',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'globals' => ['class' => 'app\components\Globals'],
        'utils' => ['class' => 'app\components\Utils'],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '!kairos#',
            'enableCsrfValidation' => false,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\web\JqueryAsset' => [
                    'js' => [],
                ],

            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Usuarios',
            //'enableAutoLogin' => true,
            "enableSession" => true,
            "authTimeout" => $timesess['time'], //(20 min) tiempo de inactividad en segundos 
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],


        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.serviciogasolina.com',
                'username' => 'alertas@serviciogasolina.com',
                'password' => '1#[Ly&rvd*Fp',
                'port' => '587',
                'encryption' => 'tls',
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]
            ],
        ],


        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    // $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        //'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [ // HERE
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    // 'Custom' => '@vendor/yiisoft/yii2-gii/src/generators/crud/custom',
                    //'querycrud' => '@vendor/yiisoft/yii2-gii/src/generators/crud/querycrud',
                    'Custom' => '@vendor/yiisoft/yii2-gii/src/generators/crud/keysquerycrud',
                ]
            ],
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    // 'Custom' => '@vendor/yiisoft/yii2-gii/src/generators/model/custom',
                    //'querycrud' => '@vendor/yiisoft/yii2-gii/src/generators/model/querycrud',
                    'Custom' => '@vendor/yiisoft/yii2-gii/src/generators/model/keysquerycrud',
                ]
            ]
        ],

        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
