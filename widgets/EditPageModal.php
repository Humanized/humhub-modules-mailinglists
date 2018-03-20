<?php

namespace humhub\modules\mailinglists\widgets;

use Yii;
use yii\helpers\Url;
use humhub\widgets\Modal;


/**
 * User Administration Menu.
 * Adapted for Mailinglists by bkfox
 *
 * @author Basti
 */
class EditPageModal extends Modal
{
    public $model;
    public $entry;
    public $space = null;

    public function run() {
        return $this->render('editPage', [
            'model' => $this->model,
            'entry' => $this->entry,
            'space' => $this->space,
        ]);
    }
}
