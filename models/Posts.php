<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $author
 * @property string|null $featured_image
 * @property string|null $excerpt
 * @property string|null $scraped_date
 * @property string|null $article_date
 */
class Posts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['excerpt'], 'string'],
            [['scraped_date', 'article_date'], 'safe'],
            [['title', 'author', 'featured_image'], 'string', 'max' => 255],
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
            'author' => 'Author',
            'featured_image' => 'Featured Image',
            'excerpt' => 'Excerpt',
            'scraped_date' => 'Scraped Date',
            'article_date' => 'Article Date',
        ];
    }
}
