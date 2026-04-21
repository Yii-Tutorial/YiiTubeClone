<?php
namespace common\models;

use Imagine\Image\Box;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use yii\imagine\Image;

/**
 * This is the model class for table "{{%videos}}".
 *
 * @property int $id
 * @property string $video_id
 * @property string $title
 * @property string|null $description
 * @property string|null $tags
 * @property int|null $status
 * @property int|null $has_thumbnail
 * @property string|null $video_name
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * 
 * @property \common\models\VideoLike[] $likes
 * @property \common\models\VideoLike[] $dislikes
 */
class Videos extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT     = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @var \yii\web\UploadedFile $video
     */
    public $video;

    /**
     * @var \yii\web\UploadedFile $thumbnail
     */
    public $thumbnail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%videos}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class'              => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'tags', 'status', 'video_name', 'updated_at', 'created_by'], 'default', 'value' => null],
            [['has_thumbnail'], 'default', 'value' => 0],
            [['video_id', 'title', 'created_at'], 'required'],
            [['description'], 'string'],
            [['status', 'has_thumbnail', 'created_at', 'updated_at', 'created_by'], 'integer'],
            [['video_id'], 'string', 'max' => 16],
            [['title', 'tags'], 'string', 'max' => 512],
            [['video_name'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['thumbnail', 'image', 'minWidth' => 1280, 'extensions' => 'jpg, jpeg, png'],
            ['video', 'file', 'extensions' => ['mp4']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'video_id'      => 'Video ID',
            'title'         => 'Title',
            'description'   => 'Description',
            'tags'          => 'Tags',
            'status'        => 'Status',
            'has_thumbnail' => 'Has Thumbnail',
            'video_name'    => 'Video Name',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
            'created_by'    => 'Created By',
            'thumbnail'     => 'Thumbnail',
        ];
    }

    public function getStatusLabel()
    {
        return [
            self::STATUS_DRAFT     => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
        ];
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getViews()
    {
        return $this->hasMany(VideoView::class, ['video_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\VideosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\VideosQuery(get_called_class());
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $isInsert = $this->isNewRecord;
        if ($isInsert) {
            if ($this->video === null) {
                return false;
            }
            $this->video_id   = Yii::$app->security->generateRandomString(16);
            $this->title      = $this->video->name;
            $this->video_name = $this->video->name;
            $this->created_at = time();
            if (! Yii::$app->user->isGuest) {
                $this->created_by = (int) Yii::$app->user->id;
            }
        }

        if ($this->thumbnail) {
            $this->has_thumbnail = 1;
        }

        $saved = parent::save($runValidation, $attributeNames);

        if (! $saved) {
            return false;
        }
        if ($isInsert) {
            $videoPath = Yii::getAlias('@frontend/web/storage/videos/' . $this->video_id . '.mp4');
            if (! is_dir(dirname($videoPath))) {
                FileHelper::createDirectory(dirname($videoPath));
            }
            $this->video->saveAs($videoPath);
        }

        if ($this->thumbnail) {
            $thumbnailPath = Yii::getAlias('@frontend/web/storage/thumbnails/' . $this->video_id . '.jpg');
            if (! is_dir(dirname($thumbnailPath))) {
                FileHelper::createDirectory(dirname($thumbnailPath));
            }
            $this->thumbnail->saveAs($thumbnailPath);
            Image::getImagine()->open($thumbnailPath)
                ->thumbnail(new Box(1280, 1280))
                ->save();
        }
        return true;
    }

    public function getVideoLink()
    {
        return Yii::$app->params['frontendUrl'] . 'storage/videos/' . $this->video_id . '.mp4';
    }

    public function getThumbnailLink()
    {
        $version = (int) ($this->updated_at ?? $this->created_at ?? time());
        return $this->has_thumbnail
            ? Yii::$app->params['frontendUrl'] . 'storage/thumbnails/' . $this->video_id . '.jpg?v=' . $version
            : null;
    }

    public function afterDelete()
    {
        return parent::afterDelete();
        $videoPath = Yii::getAlias('@frontend/web/storage/videos/' . $this->video_id . '.mp4');
        unlink($videoPath);


        $thumbnailPath = Yii::getAlias('@frontend/web/storage/thumbnails/' . $this->video_id . '.jpg');
        if (file_exists($thumbnailPath)) {
           unlink($thumbnailPath);
        }
    }

    public function isLikeBy($userId)
    {
        return VideoLike::find()
        ->userIdVideoId($userId, $this->id)
        ->liked()
        ->one();
    }

    public function isDislikeBy($userId)
    {
        return VideoLike::find()->userIdVideoId($userId, $this->id)->disliked()->one();
    }

    public function getLikes()
    {
        return $this->hasMany(VideoLike::class, ['video_id' => 'id'])->liked();
    }

    public function getDislikes()
    {
        return $this->hasMany(VideoLike::class, ['video_id' => 'id'])->disliked();
    }

    public function getComments()
    {
        return $this->hasMany(Comments::class, ['video_id' => 'id'])
            ->andWhere(['parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC]);
    }
}
