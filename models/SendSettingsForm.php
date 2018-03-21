<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;


class SendSettingsForm extends Model
{
    public $entry = 0;
    public $includePage = false;
    public $members = null;
    public $subscribers = null;

    public function rules()
    {
        return [
            ['entry', 'exist', 'targetClass' => MailingListEntry::className(),
             'targetAttribute' => 'id'],
             // TODO: targetClass
            ['members', 'each', 'rule' => ['integer']],
            ['subscribers', 'each', 'rule' => ['integer']],
        ];
    }
}



