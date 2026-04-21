<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%videos}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m260330_090130_create_videos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%videos}}', [
            'id' => $this->primaryKey(),
            'video_id' => $this->string(16)->notNull(),
            'title' => $this->string(512)->notNull(),
            'description' => $this->text(),
            'tags' => $this->string(512),
            'status' =>$this->integer(1),
            'has_thumbnail' => $this->boolean()->defaultValue(false),
            'video_name' => $this->string(255),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
        ]);
        // creates index for column `created_by`
        $this->createIndex(
            '{{%idx-videos-created_by}}',
            '{{%videos}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-videos-created_by}}',
            '{{%videos}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-videos-created_by}}',
            '{{%videos}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-videos-created_by}}',
            '{{%videos}}'
        );

        $this->dropTable('{{%videos}}');
    }
}
