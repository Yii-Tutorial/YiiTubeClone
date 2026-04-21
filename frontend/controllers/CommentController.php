<?php

namespace frontend\controllers;

class CommentController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['create'],
                'rules'
                => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate($video_id)
    {
        $videoModel = \common\models\Videos::findOne($video_id);
        $model = new \common\models\Comments();

        if ($videoModel === null) {
            throw new \yii\web\NotFoundHttpException('Video not found.');
        }

        if ($model->load(\Yii::$app->request->post())) {
            $model->video_id = $video_id;
            $model->user_id = \Yii::$app->user->id;
            $model->parent_id = \Yii::$app->request->post('parent_id') ?: null;
            $model->created_at = time();

            if ($model->save()) {
                $model = new \common\models\Comments();
            }
        }

        return $this->renderAjax('/video/_comment_section', [
            'model' => $videoModel,
            'commentModel' => $model,
            'comments' => $videoModel->getComments()->all(),
        ]);
    }
}
