<?php

namespace humhub\modules\mailing_lists\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mailing_list_member".
 *
 * @property integer $id
 * @property string $email
 */
class Membership extends ActiveRecord
{

    public static function tableName()
    {
        return 'mailing_list_membership';
    }

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'unique'],
            ['is_member', 'boolean'],
        ];
    }

}
