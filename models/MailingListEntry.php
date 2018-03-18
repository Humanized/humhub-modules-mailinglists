<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\db\ActiveRecord;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

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
 * @property integer $template_instance_id
 * @property bool $is_sent
 */
class MailingListEntry extends ActiveRecord
{
    public function getInstance()
    {
        return $this->hasOne(TemplateInstance::className(),
                             ['id' => 'template_instance_id'])->one();
    }

    public function getPage()
    {
        $instance = $this->getInstance();
        if($instance)
            return $instance->getPolymorphicRelation();
    }

    /**
     *  Return the id of the user selected template
     */
    public static function getTemplateId()
    {
        return Yii::$app->getModule('mailinglists')->settings
                    ->get('globalTemplateId',0);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_list_entry';
    }
}


