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

    public function rules()
    {
        return [
            ['entry', 'integer'],
            ['toNewsletter', 'boolean'],
            ['toMembers', 'boolean'],
            ['includePage', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            ['toNewsletter', 'Send to newsletter'],
            ['toMembers', 'Send to members'],
            ['includePage', 'Include page content']
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



