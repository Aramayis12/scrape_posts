<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TopWordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Most used words';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="top-words-index">

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
					return '<h3><span class="badge badge-primary">' . $data['title'] . '</span></h3>';
				},
				
			],
            'count',
			[
				'label' => 'Scraped Date',
				'value' => 'date',
				'format' => 'date',
				'attribute' => 'date',
				'filter' => DateRangePicker::widget([
                   'name' => 'date_range',
                   'attribute' => 'date_range',
                   'value' => Yii::$app->getRequest()->getQueryParam('date_range') ? Yii::$app->getRequest()->getQueryParam('date_range') : '',
                   'convertFormat' => true,
                   //'startAttribute'=> date('Y-m-d h:i'),
                   //'endAttribute'=>date('Y-m-d h:i'),
                   'pluginOptions' => [
                       'timePicker' => true,
                       'timePickerIncrement' => 1,
                       'locale' => [
                           'format' => 'Y-m-d'
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
