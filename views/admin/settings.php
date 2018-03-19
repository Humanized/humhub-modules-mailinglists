<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\Button;

use humhub\modules\custom_pages\controllers\ViewController;

use humhub\modules\mailinglists\widgets\AdminMenu;
use humhub\modules\mailinglists\models\Settings;

/**
 *  @param str message print info message
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
            <?= $form->field($model, 'mailHeader')->textarea() ?>
            <?= $form->field($model, 'mailBody')->textarea(['rows' => 10]) ?>
            <?= $form->field($model, 'mailSignature')->textarea(['rows' => 5]) ?>
            <?= $form->field($model, 'mailMention')->textarea(['rows' => 5]) ?>

        <p>Available dynamic content:
        <?php
            $maps = Settings::mailMapping();
            $maps = array_keys($maps);
            $maps = array_map(function($v) {
                return '<i>{{ ' . $v . ' }}</i>';
            }, $maps);
            echo join(', ', $maps);
        ?>
        </p>

        <hr>

        <?= Button::save()->submit() ?>

        <?php ActiveForm::end() ?>
        </div>
    </div>
</div>


