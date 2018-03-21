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
class SendSettingsModal extends Modal
{
    public $model;
    public $entry;
    public $space = null;
    public $members;

    public function run() {
        return $this->render('sendSettings', [
            'model' => $this->model,
            'entry' => $this->entry,
            'space' => $this->space,
            'members' => $this->members,
        ]);
    }
}
