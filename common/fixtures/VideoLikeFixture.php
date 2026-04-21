<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class VideoLikeFixture extends ActiveFixture
{
    public $modelClass = 'common\\models\\VideoLike';

    public $depends = [
        'common\\fixtures\\UserFixture',
        'common\\fixtures\\VideosFixture',
    ];
}
