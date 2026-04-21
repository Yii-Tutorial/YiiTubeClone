<?php

use yii\helpers\Html;

$likeButtonClass = $model->isLikeBy(\Yii::$app->user->id)
    ? 'btn-outline-primary'
    : 'btn-outline-secondary';
$dislikeButtonClass = $model->isDislikeBy(\Yii::$app->user->id)
    ? 'btn-outline-primary'
    : 'btn-outline-secondary';
?>

<?= Html::beginForm(['/video/like', 'id' => $model->id], 'post', [
    'class' => 'd-inline',
    'data-pjax' => 1,
    'data-testid' => 'video-like-form',
]) ?>
<?= Html::submitButton(
    '<i class="fa-solid fa-thumbs-up"></i> <span data-testid="video-like-count">' . $model->getLikes()->count() . '</span>',
    [
        'class' => 'btn btn-sm ' . $likeButtonClass,
        'data-testid' => 'video-like-button',
    ]
) ?>
<?= Html::endForm() ?>

<?= Html::beginForm(['/video/dislike', 'id' => $model->id], 'post', [
    'class' => 'd-inline',
    'data-pjax' => 1,
    'data-testid' => 'video-dislike-form',
]) ?>
<?= Html::submitButton(
    '<i class="fa-solid fa-thumbs-down"></i> <span data-testid="video-dislike-count">' . $model->getDislikes()->count() . '</span>',
    [
        'class' => 'btn btn-sm ' . $dislikeButtonClass,
        'data-testid' => 'video-dislike-button',
    ]
) ?>
<?= Html::endForm() ?>
