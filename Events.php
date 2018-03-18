<?php

namespace humhub\modules\mailinglists;

use Yii;
use yii\helpers\Url;

use humhub\modules\user\models\User;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

use humhub\modules\mailinglists\models\MailingListEntry;


/**
 *  MentionSpacesEvents
 */
class Events extends \yii\base\Object
{
    public static function onAdminMenuInit($event)
    {
        $event->sender->addItem([
            'label' => 'Mailing Lists',
            'url' => Url::to(['/mailinglists/admin']),
            'group' => 'manage',
            'icon' => '<i class="fa fa-envelope"></i>',
            'isActive' => (
                Yii::$app->controller->module &&
                Yii::$app->controller->module->id == 'mailingslist' &&
                Yii::$app->controller->id == 'admin'
            ),
            'sortOrder' => 300,
        ]);
    }


    public static function onTemplateInstanceInsert($event)
    {
        $instance = $event->sender;
        // FIXME "TemplateInstance"
        if(get_class($instance) != "humhub\modules\custom_pages\models\Page")
            return;

        $instance = TemplateInstance::find()->where([
            'object_id' => $instance->id,
            'object_model' => 'humhub\modules\custom_pages\models\Page',
        ])->one();

        // TODO: user settings
        if($instance->template_id != MailingListEntry::getTemplateId())
            return;

        $entry = MailingListEntry::find()->where([
            'template_instance_id' => $instance->id,
        ])->one();

        if($entry)
            return;

        $entry = new MailingListEntry();
        $entry->template_instance_id = $instance->id;
        $entry->is_sent = false;
        $entry->save();
    }

    // cascading does not seem to work
    public static function onTemplateInstanceDelete($event)
    {
        $instance = $event->sender;
        if(get_class($instance) != TemplateInstance::className() ||
               $instance->template_id != MailingListEntry::getTemplateId())
           return;

        $entry = MailingListEntry::find()->where([
            'template_instance_id' => $instance->id,
        ])->one();
        $entry->delete();
    }
}
