<?php

namespace humhub\modules\mailing_lists\models;

use Yii;
use yii\db\ActiveRecord;

use humhub\modules\custom_pages\models\page;

/**
 * This is the model class for table "mailing_list_entry".
 *
 * @property integer $id
 * @property string $email
 * @property bool $subscribed
 * @property string $token
 */
class Membership extends ActiveRecord
{
    public static function tableName()
    {
        return 'mailing_list_membership';
    }

    public static function rules()
    {
        return [
            [['subscibed', 'email'], 'required']
            ['email', 'email'],
        ]
    }
}


