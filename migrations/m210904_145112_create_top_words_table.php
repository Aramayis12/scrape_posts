<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%top_words}}`.
 */
class m210904_145112_create_top_words_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%top_words}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'count' => $this->integer(),
            'date' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%top_words}}');
    }
}
