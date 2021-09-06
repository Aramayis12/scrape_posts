<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Posts;

/**
 * PostsSearch represents the model behind the search form of `app\models\Posts`.
 */
class PostsSearch extends Posts
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['title', 'author', 'featured_image', 'excerpt', 'scraped_date', 'article_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Posts::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

		if(isset($params['scraped_date_range']) && $params['scraped_date_range'] != ''){
			$explode_scraped_date = explode(' - ', $params['scraped_date_range']);
			$start_date = $explode_scraped_date[0];
			$end_date = $explode_scraped_date[1];

			$query
				->andFilterWhere(['>=', 'scraped_date', $start_date])
				->andFilterWhere(['<=', 'scraped_date', $end_date]);
		}

		if(isset($params['article_date_range']) && $params['article_date_range'] != ''){
			$explode_scraped_date = explode(' - ', $params['article_date_range']);
			$start_date = $explode_scraped_date[0];
			$end_date = $explode_scraped_date[1];

			$query
				->andFilterWhere(['>=', 'article_date', $start_date])
				->andFilterWhere(['<=', 'article_date', $end_date]);
		}

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'featured_image', $this->featured_image])
            ->andFilterWhere(['like', 'excerpt', $this->excerpt]);

		$query->cache(120); // 2 min

        return $dataProvider;
    }
}
