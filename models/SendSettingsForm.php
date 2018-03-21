<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;


class SendSettingsForm extends Model
{
    public $entry = 0;
    public $toNewsletter = false;
    public $toMembers = false;
    public $includePage = false;
    public $members = null;

    public function rules()
    {
        return [
            ['entry', 'exist', 'targetClass' => MailingListEntry::className(),
             'targetAttribute' => 'id'],
            ['toNewsletter', 'boolean'],
            ['toMembers', 'boolean'],
            ['includePage', 'boolean'],
            ['members', 'each', 'rule' => ['string']]
        ];
    }

    public function attributeLabels()
    {
        return [
            ['toNewsletter', 'Send to newsletter'],
            ['toMembers', 'Send to members'],
            ['includePage', 'Include page content in the mail instead'],
            ['members', 'Select members']
        ];
    }

    public function attributeHints()
    {
        return [
            ['toNewsletter', 'Send to all people subscribed to the newsletter'],
            ['toMembers', 'Send to all the member of the social network'],
            ['includePage',
             'Include the content of the edited page instead of using '.
                'default body'],
        ];
    }
}



