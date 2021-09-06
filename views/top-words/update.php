<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TopWords */

$this->title = 'Update Top Words: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Top Words', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="top-words-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
