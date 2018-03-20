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
        <?php if($space) { ?>
        This mail will be sent to all members of <?= $space->displayName ?>.
        <?= $form->field($model, 'toNewsletter')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'toMembers')->hiddenInput()->label(false) ?>
        <?php } else { ?>
        <?= $form->field($model, 'toNewsletter')->checkbox() ?>
        <?= $form->field($model, 'toMembers')->checkbox() ?>
        <br>
        <?php } ?>
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

