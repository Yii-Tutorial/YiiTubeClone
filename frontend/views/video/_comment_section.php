<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

?>

<h6>Comments</h6>
<div class="comment-form">
    <?php $form = ActiveForm::begin([
        'action' => ['comment/create', 'video_id' => $model->id],
        'options' => ['data-pjax' => 1]
    ]); ?>

    <?= $form->field($commentModel, 'content')->textarea([
        'rows' => 3,
        'placeholder' => 'Write a comment...',
        'class' => 'form-control'
    ])->label(false) ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Comment', ['class' => 'btn btn-primary mt-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<div class="comment-list">
    <?php if (empty($comments)): ?>
        <p class="text-muted">No comments yet.</p>
    <?php endif; ?>

    <?php foreach ($comments as $comment): ?>
        <div class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex inline-block gap-2 mb-2">
                        <strong><?php echo Html::channelLink($comment->user) ?></strong>
                        <p class="text-muted"><?php echo \Yii::$app->formatter->asRelativeTime($comment->created_at); ?></p>
                    </div>

                    <p><?= \yii\helpers\Html::encode($comment->content) ?></p>
                    <button
                        type="button"
                        class="btn btn-link btn-sm p-0 mt-1"
                        data-bs-toggle="collapse"
                        data-bs-target="#reply-form-<?php echo $comment->id ?>"
                        aria-expanded="false"
                        aria-controls="reply-form-<?php echo $comment->id ?>"
                    >
                        Reply
                    </button>

                    <div class="collapse mt-3" id="reply-form-<?php echo $comment->id ?>">
                        <?php $replyForm = ActiveForm::begin([
                            'action' => ['comment/create', 'video_id' => $model->id],
                            'options' => ['data-pjax' => 1]
                        ]); ?>

                        <?= \yii\helpers\Html::hiddenInput('parent_id', $comment->id) ?>

                        <?= $replyForm->field($commentModel, 'content')->textarea([
                            'rows' => 2,
                            'placeholder' => 'Write a reply...',
                            'class' => 'form-control'
                        ])->label(false) ?>

                        <div class="form-group">
                            <?= yii\helpers\Html::submitButton('Reply', ['class' => 'btn btn-outline-primary btn-sm']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>

                    <?php foreach ($comment->comments as $reply): ?>
                        <div class="reply ms-4 mt-3 border-start ps-3">
                            <div class="d-flex inline-block gap-2 mb-2">
                                <strong><?php echo Html::channelLink($reply->user) ?></strong>
                                <p class="text-muted"><?php echo \Yii::$app->formatter->asRelativeTime($reply->created_at); ?></p>
                            </div>
                            <p class="mb-0"><?= \yii\helpers\Html::encode($reply->content) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
