<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\MarkdownEditor;
use humhub\widgets\ModalDialog;

use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;

use humhub\modules\mailinglists\models\Settings;
use humhub\modules\mailinglists\widgets\AdminMenu;

$page = $entry->page;
$title = 'Edit mail';
$settings = new Settings(['space' => $space ]);
?>
<!-- TODO: assets -->
<style>
.mlPreviewContainer {
    border: 1px rgba(0,0,0,0.3) solid;
    box-shadow: inset 0em 0em 0.4em rgba(0,0,0,0.3);
    padding: 1em;
}
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Mailing-lists</strong>
    </div>

    <?= AdminMenu::widget(['space' => $space]) ?>

    <div class="panel-body">
        <button class="btn btn-primary pull-right"
            data-action-click="ui.modal.load"
            data-action-url="<?= Url::toRoute(
                $space ?
                    ['container/send', 'id' => $entry->id,
                        'sguid' => $space->guid] :
                    ['admin/send', 'id' => $entry->id],
                true
            )
            ?>"
            title="Send mails"
        >
            <li class="fa fa-envelope"></li>
            Send
        </button>

        <h5>General</h5>
        <?php
            $form = ActiveForm::begin([
                'action' => $space ?
                    $space->createUrl('container/edit', ['id' => $entry->id]) :
                    Url::to(["admin/edit", 'id' => $entry->id])
            ])
        ?>

        <?= $form->field($model, 'entry')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'subject')->textInput() ?>
        <div style="text-align:right;">
            <button class="btn btn-success">
                <li class="fa fa-envelope"></li>
                Save
            </button>
        </div>


        <?php ActiveForm::end() ?>
        <hr/>
        <h5>Body</H5>
        <div class="mlPreviewContainer">
        <?php TemplatePage::begin(['page' => $page, 'canEdit' => true, 'editMode' => true]) ?>
        <?= $entry->getPageContent(true) ?>
        <?php TemplatePage::end() ?>
        </div>
        <hr>
    </div>
</div>

