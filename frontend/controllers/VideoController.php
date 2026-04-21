<?php

namespace frontend\controllers;

use common\models\Comments;
use common\models\VideoLike;
use common\models\Videos;
use common\models\VideoView;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class VideoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['like', 'dislike'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'like' => ['post'],
                    'dislike' => ['post'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => Videos::find()->with('createdBy')->published()->latest()
            ]
        );
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id)
    {
        $this->layout = 'auth';
        $model = $this->findVideo($id);
        $commentModel = new Comments();

        $videoView = new VideoView();
        $videoView->video_id = $id;
        $videoView->user_id = \Yii::$app->user->id;
        $videoView->created_at = time();
        $videoView->save();

        $similarVideo = Videos::find()
            ->published()
            ->andWhere(['!=', 'id', $id])
            ->byKeyword($model->title)
            ->limit(10)
            ->all();

        
        return $this->render('view', [
            'model' => $model,
            'similarVideo' => $similarVideo,
            'commentModel' => $commentModel,
        ]);
    }

    public function actionLike($id)
    {
        $video = $this->findVideo($id);

        $videoLikeDisLike = VideoLike::find()
        ->userIdVideoId(\Yii::$app->user->id, $id)
        ->one();

        if(!$videoLikeDisLike){
            $this->saveLikeDislike($id, VideoLike::TYPE_LIKE);
        }
        else if ($videoLikeDisLike->type == VideoLike::TYPE_LIKE) {
            $videoLikeDisLike->delete();
        } else {
            $videoLikeDisLike->delete();
            $this->saveLikeDislike($id, VideoLike::TYPE_LIKE);
        }


        return $this->renderAjax('_like_dislike', [
            'model' => $video
        ]);
    }

    public function actionDislike($id)
    {
        $video = $this->findVideo($id);

        $videoLikeDisLike = VideoLike::find()
        ->userIdVideoId(\Yii::$app->user->id, $id)
        ->one();

        if(!$videoLikeDisLike){
            $this->saveLikeDislike($id, VideoLike::TYPE_DISLIKE);
        }
        else if ($videoLikeDisLike->type == VideoLike::TYPE_DISLIKE) {
            $videoLikeDisLike->delete();
        } else {
            $videoLikeDisLike->delete();
            $this->saveLikeDislike($id, VideoLike::TYPE_DISLIKE);
        }

        return $this->renderAjax('_like_dislike', [
            'model' => $video
        ]);
    }

    protected function saveLikeDislike($videoId, $type)
    {

        $videoLikeDisLike = new VideoLike();
        $videoLikeDisLike->video_id = $videoId;
        $videoLikeDisLike->user_id = \Yii::$app->user->id;
        $videoLikeDisLike->type = $type;
        $videoLikeDisLike->created_at = time();
        $videoLikeDisLike->save();
    }

    public function findVideo($id)
    {
        if (($model = Videos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested video does not exist.');
    }

    public function actionSearch($keyword)
    {
        $query = Videos::find()
            ->published()
            ->latest()
            ->byKeyword($keyword);
        
        if ($keyword)
        {
            $query->byKeyword($keyword);
        }
        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query
             ]
        );
        return $this->render('search', ['dataProvider' => $dataProvider]);
    }

    public function actionHistory()
    {
        $query = Videos::find()
            ->alias('v')
            ->innerJoin("(select video_id, user_id, MAX(created_at) as max_date 
                    from video_view vv  
                    where user_id = :userId
                    group by vv.video_id) as vv", 'vv.video_id = v.id' ,[':userId' => \Yii::$app->user->id])
            ->orderBy(['vv.max_date' => SORT_DESC]);

        
        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query
             ]
        );
        return $this->render('history', ['dataProvider' => $dataProvider]);
    }
}