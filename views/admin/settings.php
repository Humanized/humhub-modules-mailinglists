<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\Button;

use humhub\modules\custom_pages\controllers\ViewController;

use humhub\modules\mailinglists\widgets\AdminMenu;
use humhub\modules\mailinglists\models\MailingListEntry;

/**
 *  @param str $message: print info message
 *  @param Space|null $space: current space
 *  @param Settings $model: settings model
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Mailing-lists</strong>
    </div>

    <?= AdminMenu::widget([ 'space' => $space]) ?>

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

        <div class="clearfix">
        <?php $form = ActiveForm::begin() ?>
            <?= $form->field($model, 'mailTemplate')->dropDownList(
                    $model->templates, ['value' => $model->mailTemplate]) ?>

            <?php if(!$space) { ?>
            <?= $form->field($model, 'mailBody')->textarea(['rows' => 10]) ?>
            <?= $form->field($model, 'mailMention')->textarea(['rows' => 5]) ?>

            <p>These values can be used in mail body and mention:
            <?php
                $maps = MailingListEntry::valuesMap();
                $maps = array_keys($maps);
                $maps = array_map(function($v) {
                    return '<i>{{ ' . $v . ' }}</i>';
                }, $maps);
                echo join(', ', $maps);
            ?>
            </p>
            <?php } else { ?>
            <?= $form->field($model, 'mailBody')->hiddenInput()->label(false)->hint(false) ?>
            <?= $form->field($model, 'mailMention')->hiddenInput()->label(false)->hint(false) ?>
            <?php } ?>
        <hr>

        <?= Button::save()->submit() ?>

        <?php ActiveForm::end() ?>
        </div>
    </div>
</div>


