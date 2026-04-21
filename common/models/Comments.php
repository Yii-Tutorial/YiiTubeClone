<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%comments}}".
 *
 * @property int $id
 * @property int $video_id
 * @property int $user_id
 * @property string $content
 * @property int $created_at
 * @property int|null $parent_id
 *
 * @property Comments[] $comments
 * @property Comments $parent
 * @property User $user
 * @property Videos $video
 */
class Comments extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comments}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['video_id', 'user_id', 'content', 'created_at'], 'required'],
            [['video_id', 'user_id', 'created_at', 'parent_id'], 'integer'],
            [['content'], 'string'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comments::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['video_id'], 'exist', 'skipOnError' => true, 'targetClass' => Videos::class, 'targetAttribute' => ['video_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'video_id' => 'Video ID',
            'user_id' => 'User ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CommentsQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['parent_id' => 'id'])
            ->with('user')
            ->orderBy(['created_at' => SORT_ASC]);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CommentsQuery
     */
    public function getParent()
    {
        return $this->hasOne(Comments::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Video]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\VideosQuery
     */
    public function getVideo()
    {
        return $this->hasOne(Videos::class, ['id' => 'video_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CommentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CommentsQuery(get_called_class());
    }

}
