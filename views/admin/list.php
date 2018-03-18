<?php

use yii\helpers\Html;
use yii\helpers\Url;

use humhub\modules\mailinglists\widgets\AdminMenu;

/**
 *  @param [MailingListEntry] $entries
 *  @param str $message print informational message
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Mailing-lists</strong>
    </div>

    <?= AdminMenu::widget() ?>

    <div class="panel-body">
        <div class="clearfix">
            <h4>Manage Mails</h4>
            <?php
                if(isset($message) && $message) {
                    echo '<div>' . $message . '</div>';
                }
            ?>
        </div>
        <hr>


        <div class="clearfix">
            <h5>Create mail</h5>
            <form method="POST" action="<?= Url::to(["admin/new-page"]) ?>"
                style="display: flex; flex-orientation:row;"
                >
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <input type="text" class="form-control" flex="1" placeHolder="Mail Subject" name="title">
                <button class="btn btn-success" style="display: inline;" >
                    <li class="fa fa-plus">
                        Create new mail
                    </li>
                </button>
            </form>
        </div>
        <hr>


        <h5>Manage mails</h5>
        <table class="table"><tbody>
            <tr>
                <th style="min-width: 60%;">Title</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        <?php

        foreach($entries as $entry) {
                $page = $entry->page;
            ?>
            <tr style="<?php
                if(!$entry->is_sent)
                    echo "background-color:rgba(255, 160, 0, 0.2);";
                else
                    echo "background-color:rgba(180, 255, 0, 0.2);";
                ?>">
                <td><?= $page->title ?></td>
                <td>
                    <?= ($entry->is_sent ? "Sent" : "<strong>Not Sent</strong>") ?>
                </td>
                <td>
                    <div class="pull-right">
                        <form style="display:inline-block" method="POST" action="<?= Url::to(['admin/send']) ?>">
                            <input type="hidden" name="entry" value="<?= $entry->id ?>">
                            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                            <button class="btn btn-xs btn-primary">
                                <li class="fa fa-envelope">
                                    Send
                                </li>
                            </button>
                        </form>
                        <?= Html::a(
                            '<li class="fa fa-pencil" title="Edit"></li>',
                            ['/custom_pages/view/view', 'id' => $page->id, 'editMode' => 1],
                            ['class' => 'btn btn-xs btn-primary']
                        ) ?>
                        <?= Html::a(
                            '<li class="fa fa-cogs" title="Page Settings"></li>',
                            ['/custom_pages/admin/edit', 'id' => $page->id],
                            ['class' => 'btn btn-xs btn-primary']
                        ) ?>
                        <?= Html::a(
                            '<li class="fa fa-trash" title="Delete Page"> Delete</li>',
                            ['/custom_pages/admin/delete', 'id' => $page->id],
                            ['class' => 'btn btn-xs btn-danger']
                        ) ?>
                    </div>
                </td>
            </tr>
            <?php
        }

        ?>
        </tbody></table>
    </div>
</div>
