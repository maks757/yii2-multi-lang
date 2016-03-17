<?php

use yii\helpers\Url;
?>

<div class="btn-group">
    <button data-toggle="dropdown" class="btn btn-warning btn-xs dropdown-toggle">
        <?= Yii::t('common', $current->name) ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <?php foreach ($languages as $language): ?>
            <li>
                <?= yii\helpers\Html::a(Yii::t('common', $language->name), ['language' => $language->lang_id]) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
