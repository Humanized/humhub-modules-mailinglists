<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\ModalDialog;

use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;

$page = $entry->page;
$title = 'Send a mail: ' . $page->title;
?>
<style>
#mlPreviewContainer {
    border: 1px rgba(0,0,0,0.3) solid;
    box-shadow: inset 0em 0em 0.4em rgba(0,0,0,0.3);
    padding: 1em;
}

#mlPreviewContainer > div {
    display: none;
}

#mlPreviewContainer[includePage] > .ml_with_page,
#mlPreviewContainer:not([includePage]) > .ml_without_page {
    display: block;
}
</style>
<script>
function mlMailSetPreview(includePage) {
    var container = document.getElementById('mlPreviewContainer');
    if(includePage)
        container.setAttribute('includePage', '1');
    else
        container.removeAttribute('includePage');
}

</script>


<?php ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>
<div class="modal-body media-body">
<?php
    $form = ActiveForm::begin([
        'action' => $space ?
            $space->createUrl('container/send', ['id' => $entry->id]) :
            Url::to(["admin/send", 'id' => $entry->id])
    ])
?>
        <h4>Settings</h4>
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
        <?= $form->field($model, 'includePage')->checkbox([
            'id' => 'includePage',
            'onchange' => 'mlMailSetPreview(this.checked)'
        ]) ?>

        <div style="text-align:right;" >
            <?= Html::a(
                '<li class="fa fa-pencil"></li> Edit',
                $space ?
                    $space->createUrl('container/edit', ['id' => $entry->id]) :
                    Url::to(["admin/edit", 'id' => $entry->id]),
                ['class' => 'btn btn-primary',
                 'title' => 'Edit mail']
            ) ?>

            <button class="btn btn-success">
                <li class="fa fa-envelope"></li>
                Send!
            </button>
        </div>
    <?php ActiveForm::end() ?>

    <hr>
    <h4>Preview</h4>
    <div id="mlPreviewContainer">
        <div class="ml_without_page">
        <?= $entry->renderMail() ?>
        </div>
        <div class="ml_with_page">
        <?php /* can't edit inline even when assets in custom_pages/modules/templates/assets are all loaded *//* TemplatePage::begin(['page' => $page, 'canEdit' => true, 'editMode' => true]) */ ?>
        <?= $entry->getPageContent(true) ?>
        <?php /* TemplatePage::end() */ ?>
        </div>
    </div>

</div>
<?php ModalDialog::end() ?>

