<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "top_words".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $count
 * @property string|null $date
 */
class TopWords extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'top_words';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count'], 'integer'],
            [['date'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'count' => 'Count',
            'date' => 'Date',
        ];
    }
}
