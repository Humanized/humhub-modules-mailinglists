<?php

namespace humhub\modules\mailing_lists;

use Yii;

use humhub\modules\user\models\User;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

use humhub\modules\mailing_lists\models\MailingListEntry;


/**
 *  MentionSpacesEvents
 */
class Events extends \yii\base\Object
{
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
        if($instance->template_id != 8)
            return;

        $entry = MailingListEntry::find()->where([
            'template_instance_id' => $instance->id,
        ])->one();

        if($entry)
            return;

        $entry = new MailingListEntry();
        $entry->template_instance_id = $instance->id;
        $entry->sent = false;
        $entry->save();
    }
}
