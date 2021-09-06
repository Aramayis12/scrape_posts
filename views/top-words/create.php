<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TopWords */

$this->title = 'Create Top Words';
$this->params['breadcrumbs'][] = ['label' => 'Top Words', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="top-words-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
