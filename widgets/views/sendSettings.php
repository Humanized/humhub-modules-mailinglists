<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\ModalDialog;

use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;

$page = $entry->page;
$title = 'Send a mail: ' . $page->title;

/**
 *  @param SendSettingsForm $model
 *  @param MailingListEntry $entry
 *  @param Space $space
 *  @param ["id" => "label"] $members
 *
 */

?>
<style>

#sendsettingsform-preview {
    border: 1px rgba(0,0,0,0.3) solid;
    box-shadow: inset 0em 0em 0.4em rgba(0,0,0,0.3);
    padding: 1em;
}

#sendsettingsform-preview > div {
    display: none;
}

#sendsettingsform-preview[includePage] > .sendsettingsform-preview-include-page,
#sendsettingsform-preview:not([includePage]) > .sendsettingsform-preview-no-page {
    display: block;
}

.sendsettingsform-list {
    display: inline-block;
    width: calc(50% - 2em);
    margin: 0em 1em;
    vertical-align: top;
}

.sendsettingsform-list:not(:last-child) {
    padding-right: 2em;
    width: calc(50% - 3em);
    border-right: 1px solid rgba(0,0,0,0.3);
}

#sendsettingsform-members,
#sendsettingsform-subscribers {
    height: 10em;
    overflow-y: scroll;
}

</style>
<script>
function sendSettingsFormPreview(includePage) {
    var container = document.getElementById('sendsettingsform-preview');
    if(includePage)
        container.setAttribute('includePage', '1');
    else
        container.removeAttribute('includePage');
}

function sendSettingsFormSelect(container, checked, invert) {
    document.querySelectorAll('#' + container + ' input[type="checkbox"]')
        .forEach(function(cb) {
            cb.checked = checked || (invert && !cb.checked);
        });
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
    <?= $form->field($model, 'entry')->hiddenInput()->label(false) ?>

    <h4>Select People</h4>
    <div>
        <?php foreach(['members','subscribers'] as $name) { ?>
        <div class="sendsettingsform-list">
            <div class="pull-right">
                <input type="button" value="All" class="btn btn-xs"
                    onclick="sendSettingsFormSelect('sendsettingsform-<?= $name ?>', true)">
                <input type="button" value="None" class="btn btn-xs"
                    onclick="sendSettingsFormSelect('sendsettingsform-<?= $name ?>', false)">
                <input type="button" value="Invert" class="btn btn-xs"
                    onclick="sendSettingsFormSelect('sendsettingsform-<?= $name ?>', false, true)">
            </div>
            <?php
                $list = [];
                foreach($$name as $m) {
                    $list[$m->id] = $m->getDisplayName();
                }

                echo $form->field($model, $name)->checkBoxList($list);
            ?>
        </div>
        <?php } ?>
    </div>

    <h4>Mail Options</h4>
    <?= $form->field($model, 'includePage')->checkbox([
        'id' => 'includePage',
        'onchange' => 'sendSettingsFormPreview(this.checked)'
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
    <div id="sendsettingsform-preview">
        <div class="sendsettingsform-preview-no-page">
        <?= $entry->renderMail() ?>
        </div>
        <div class="sendsettingsform-preview-include-page">
        <?php TemplatePage::begin(['page' => $page, 'canEdit' => false, 'editMode' => false]) ?>
        <?= $entry->getPageContent(false) ?>
        <?php TemplatePage::end() ?>
        </div>
    </div>
</div>
<?php ModalDialog::end() ?>

