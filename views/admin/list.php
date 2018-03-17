<div class="panel panel-default">
<div class="panel-heading">
    <strong>Manage mails</strong>
</div>
<div class="panel-body">


<?php

foreach($entries as $entry) {
    $page = $entry->instance->page;
    ?>
    <div class="entry">
        <?= $page->title ?>
    </div>
    <?php
}


?>
</div>
</div>
