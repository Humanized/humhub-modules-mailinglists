<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\MarkdownEditor;
use humhub\widgets\ModalDialog;

use humhub\modules\mailinglists\models\Settings;

$page = $entry->page;
$title = 'Edit mail';
$settings = new Settings(['contentContainer' => $space ]);
?>
<?php ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>
<div class="modal-body media-body">
<?php
    $form = ActiveForm::begin([
        'action' => $space ?
            $space->createUrl('container/edit-page', ['entry' => $entry->id]) :
            Url::to(["admin/edit-page", 'entry' => $entry->id])
    ])
?>

        <?= $form->field($model, 'entry')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'subject')->textInput() ?>
        <hr/>

        <div style="font-style: italic; font-size: 0.9em; margin: 0.4em 0em;">
        <?= $settings->mailHeader ?>
        </div>

        <?= $form->field($model, 'content')->textarea([
                'id' => 'markdownField', 'class' => 'form-control',
                'rows' => '15']
            )->label(false)
        ?>
        <?= MarkdownEditor::widget(['fieldId' => 'markdownField']) ?>

        <div style="font-style: italic; font-size: 0.9em; margin: 0.4em 0em;">
        <?= $settings->mailSignature ?>
        </div>
        <div style="font-style: italic; font-size: 0.8em; margin: 0.4em 0em;">
        <?= $settings->mailMention ?>
        </div>
        <hr>
        <div style="text-align:right;" >
            <button class="btn btn-success">
                <li class="fa fa-envelope"></li>
                Save
            </button>
        </div>
    <?php ActiveForm::end() ?>
</div>
<?php ModalDialog::end() ?>

