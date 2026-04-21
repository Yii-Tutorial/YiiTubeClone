<?php

namespace common\fixtures;
use yii\test\ActiveFixture;

class SubscriberFixture extends ActiveFixture
{
    public $modelClass = 'common\\models\\Subscriber';

    public $depends = [
        'common\\fixtures\\UserFixture',
        'common\\fixtures\\VideosFixture',
    ];
}