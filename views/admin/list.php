<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Manage mails</strong>
        <?php
            if(isset($message) && $message) {
                echo $message;
            }
        ?>
    </div>
    <div class="panel-body">
        <table class="table"><tbody>
            <tr>
                <th>Title</th>
                <th>Action</th>
            </tr>
        <?php

        foreach($entries as $entry) {
                $page = $entry->page;
            ?>
            <tr style="<?php
                if(!$entry->is_sent)
                    echo "background-color:rgba(255, 180, 0, 0.2);";
                else
                    echo "background-color:rgba(180, 255, 0, 0.2);";
                ?>">
                <td><?= $page->title ?></td>
                <td><form method="POST" action="<?= Url::to(['admin/send']) ?>">
                    <input type="hidden" name="entry" value="<?= $entry->id ?>">
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                    <button name="action" value="send">
                    <?php
                    if(!$entry->is_sent)
                        echo 'Send';
                    else
                        echo 'Send Again';
                    ?>
                </form></td>
            <strong>
            </div>
            <?php
        }

        ?>
        </tbody></table>
    </div>
</div>
