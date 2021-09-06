<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '10Web’s latest blog posts';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="posts-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

			[
				'attribute' => 'title',
				'format' => 'html',
				'label' => 'Title',
				'value' => function ($data) {
					return $data['title'];
				},
				
			],
            'author',
			[
				'label' => 'Featured Image',
				'format' => 'html',
				'label' => 'Featured Image',
				'value' => function ($data) {
					return Html::img('/' . $data['featured_image'], ['width' => '100px']);
				},
			],
            'excerpt:html',
			[
               'label' => 'Scraped Date',
               'value' => 'scraped_date',
				'format' => 'datetime',
				'attribute' => 'scraped_date',
               'filter' => DateRangePicker::widget([
                   'name' => 'scraped_date_range',
                   'attribute' => 'scraped_date_range',
                   'value' => Yii::$app->getRequest()->getQueryParam('scraped_date_range') ? Yii::$app->getRequest()->getQueryParam('scraped_date_range') : '',
                   'convertFormat' => true,
                   //'startAttribute'=> date('Y-m-d h:i'),
                   //'endAttribute'=>date('Y-m-d h:i'),
                   'pluginOptions' => [
                       'timePicker' => true,
                       'timePickerIncrement' => 1,
                       'locale' => [
                           'format' => 'Y-m-d h:i:s'
                       ]
                   ]
               ])
			],
			[
               'label' => 'Article Date',
               'value' => 'article_date',
				'format' => 'datetime',
				'attribute' => 'article_date',
               'filter' => DateRangePicker::widget([
                   'name' => 'article_date_range',
                   'attribute' => 'article_date_range',
                   'value' => Yii::$app->getRequest()->getQueryParam('article_date_range') ? Yii::$app->getRequest()->getQueryParam('article_date_range') : '',
                   'convertFormat' => true,
                   //'startAttribute'=> date('Y-m-d h:i'),
                   //'endAttribute'=>date('Y-m-d h:i'),
                   'pluginOptions' => [
                       'timePicker' => true,
                       'timePickerIncrement' => 1,
                       'locale' => [
                           'format' => 'Y-m-d h:i:s'
                       ]
                   ]
               ])
			],
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	</div>

    <?php Pjax::end(); ?>

</div>
