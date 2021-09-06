<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TopWords;

/**
 * TopWordsSearch represents the model behind the search form of `app\models\TopWords`.
 */
class TopWordsSearch extends TopWords
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'count'], 'integer'],
            [['title', 'date'], 'safe'],
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
        $query = TopWords::find();

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
            'count' => $this->count,
        ]);

		if(isset($params['date_range']) && $params['date_range'] != ''){
			$explode_scraped_date = explode(' - ', $params['date_range']);
			$start_date = $explode_scraped_date[0];
			$end_date = $explode_scraped_date[1];

			$query
				->andFilterWhere(['>=', 'date', $start_date])
				->andFilterWhere(['<=', 'date', $end_date]);
		}

        $query->andFilterWhere(['like', 'title', $this->title]);

		$query->cache(120); // 2 min

        return $dataProvider;
    }
}
