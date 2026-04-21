<?php

    /** @var \common\models\Videos $model */
    /** @var \common\models\Videos[] $similarVideo */

    use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>

<div class="row">
    <div class="col-sm-8">
        <div class="ratio ratio-16x9 mb-3">
            <video src="<?php echo $model->getVideoLink() ?>"
            poster="<?php echo $model->getThumbnailLink() ?>"controls></video>
        </div>
        <h6 class='mt-2'><?php echo $model->title ?></h6>
        <div  class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <?php echo $model->getViews()->count() ?> views . <?php echo \Yii::$app->formatter->asDate($model->created_at) ?>
            </div>
            <div>
                <?php Pjax::begin([
                    'id' => 'like-dislike-pjax-container',
                    'enablePushState' => false,
                    'enableReplaceState' => false,
                ]) ?>
                <?php echo $this->render('_like_dislike', [
                        'model' => $model,
                ]) ?>
                <?php Pjax::end()?>
            </div>
        </div>
        <div>
            <p>
                <?php echo common\helpers\Html::channelLink($model->createdBy) ?>
            </p>
            <p><?php echo Html::encode($model->description) ?></p>
        </div>
    </div>
    <div class="col-sm-4">
        <?php foreach ($similarVideo as $video) : ?>
            <div class="d-flex align-items-start mb-3">
                <a href="<?php echo Url::to(['video/view', 'id' => $video->id]) ?>">
                    <div class="flex-shrink-0 me-2" style="width: 120px">
                        <div class="ratio ratio-16x9">
                            <video src="<?php echo $video->getVideoLink() ?>"
                            poster="<?php echo $video->getThumbnailLink() ?>"></video>
                        </div>
                    </div>
                </a>
                <div class="flex-grow-1">
                    <a class="text-decoration-none text-dark" href="<?php echo Url::to(['video/view', 'id' => $video->id]) ?>">
                        <h6 class='m-0'><?php echo $video->title ?></h6>
                    </a>
                    <div class="text-muted">
                        <p class="m-0">
                            <?php echo common\helpers\Html::channelLink($video->createdBy) ?>
                        </p>
                        <p>
                            <?php echo $video->getViews()->count() ?> views . <?php echo \Yii::$app->formatter->asRelativeTime($video->created_at) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>