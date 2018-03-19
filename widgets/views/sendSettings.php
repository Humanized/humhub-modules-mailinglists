<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\ModalDialog;

$page = $entry->page;
$title = 'Send a mail: ' . $page->title;
?>
<?php ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>
<div class="modal-body media-body">
<?php
    $form = ActiveForm::begin([
        'action' => $space ?
            $space->createUrl('container/send') :
            Url::to(["admin/send"])
    ])
?>
        <?= $form->field($model, 'entry')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'toNewsletter')->checkbox() ?>
        <?= $form->field($model, 'toMembers')->checkbox() ?>
        <br>
        <?= $form->field($model, 'includePage')->checkbox() ?>
        <hr>
        <div style="text-align:right;" >
            <button class="btn btn-success">
                <li class="fa fa-envelope"></li>
                Send!
            </button>
        </div>
    <?php ActiveForm::end() ?>
</div>
<?php ModalDialog::end() ?>

