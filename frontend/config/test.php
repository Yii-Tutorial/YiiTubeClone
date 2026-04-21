<?php

return [
    'id' => 'app-frontend-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'frontend-test-secret-key',
            'csrfParam' => '_csrf-frontend-test',
            'csrfCookie' => [
                'name' => '_csrf-frontend-test',
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
        'session' => [
            'name' => 'FRONTEND-TESTSESSID',
            'cookieParams' => [
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
        'mailer' => [
            'messageClass' => \yii\symfonymailer\Message::class,
        ],
        'user' => [
            'identityClass' => \common\models\User::class,
            'enableAutoLogin' => false,
            'identityCookie' => [
                'name' => '_identity-frontend-test',
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
    ],
];