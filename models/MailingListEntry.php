<?php

namespace humhub\modules\mailing_lists\models;

use Yii;
use yii\db\ActiveRecord;

use humhub\modules\custom_pages\models\Page;

/*
use yii\helpers\ArrayHelper;
use humhub\modules\content\models\Content;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;
use humhub\modules\user\models\User;
*/


/**
 * This is the model class for table "mailing_list_entry".
 *
 * @property integer $id
 * @property integer $template_instance
 * @property bool $sent
 */
class MailingListEntry extends ActiveRecord
{
    public function getInstance()
    {
        return $this->hasOne(Page::className(), ['id' => 'template_instance_id']);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_list_entry';
    }
}


