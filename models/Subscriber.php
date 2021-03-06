<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mailing_list_member".
 *
 * @property integer $id
 * @property string $email
 */
class Subscriber extends ActiveRecord
{

    public static function tableName()
    {
        return 'mailing_list_subscriber';
    }

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'unique'],
            ['is_member', 'boolean'],
        ];
    }

    public function getDisplayName()
    {
        return $this->email;
    }

}
