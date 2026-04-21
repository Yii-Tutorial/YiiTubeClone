<?php

use common\models\User;

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
            'cookieValidationKey' => 'abcxyz-test-secret-key',
            'csrfParam' => '_csrf-abcxyz-test',
            'csrfCookie' => [
                'name' => '_csrf-abcxyz-test',
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
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'identityCookie' => [
                'name' => '_identity-frontend-test',
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
    ],
];