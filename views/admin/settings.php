<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use humhub\widgets\Button;

use humhub\modules\custom_pages\controllers\ViewController;

use humhub\modules\mailinglists\widgets\AdminMenu;

/**
 *  @param str message print info message
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Mailing-lists</strong>
    </div>

    <?= AdminMenu::widget() ?>

    <div class="panel-body">
        <div class="clearfix">
            <h4>Settings</h4>
            <?php
                if(isset($message) && $message) {
                    echo '<div>' . $message . '</div>';
                }
            ?>
        </div>
        <hr/>

        <?php $form = ActiveForm::begin() ?>
            <div id="globalSettings">
                <?= $form->field($model, 'globalTemplate')->dropDownList(
                    $model->templates, ['value' => $model->globalTemplate]) ?>
                <?= $form->field($model, 'globalSignature')->textarea() ?>
            </div>
        <hr>

        <?= Button::save()->submit() ?>

        <?php ActiveForm::end() ?>
    </div>
</div>


