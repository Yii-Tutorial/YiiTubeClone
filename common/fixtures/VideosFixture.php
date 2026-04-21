<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class VideosFixture extends ActiveFixture
{
    public $modelClass = 'common\\models\\Videos';

    public $depends = [
        'common\\fixtures\\UserFixture',
    ];
}
