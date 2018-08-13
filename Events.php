<?php

namespace humhub\modules\mailinglists;

use Yii;
use yii\helpers\Url;

use humhub\modules\user\models\User;
use humhub\modules\custom_pages\models\Page;

use humhub\modules\mailinglists\models\MailingListEntry;
use humhub\modules\mailinglists\models\Settings;


/**
 *  MentionSpacesEvents
 */
class Events extends \yii\base\BaseObject
{
    public static function onAdminMenuInit($event)
    {
        $event->sender->addItem([
            'label' => 'Mailing-Lists',
            'url' => Url::to(['/mailinglists/admin']),
            'group' => 'manage',
            'icon' => '<i class="fa fa-newspaper-o"></i>',
            'isActive' => (
                Yii::$app->controller->module &&
                Yii::$app->controller->module->id == 'mailingslist' &&
                Yii::$app->controller->id == 'admin'
            ),
            'sortOrder' => 300,
        ]);
    }

    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;
        if(!$space->isModuleEnabled('mailinglists') || !$space->isAdmin())
            return;

        $event->sender->addItem([
            'label' => 'Mailing-Lists',
            'url' => $space->createUrl('/mailinglists/container'),
            'group' => 'admin',
            'icon' => '<i class="fa fa-newspaper-o"></i>',
            'isActive' => (
                Yii::$app->controller->module &&
                Yii::$app->controller->module->id == 'mailingslist' &&
                Yii::$app->controller->id == 'container'
            ),
        ]);
    }

    // cascading does not seem to work
    public static function onPageDelete($event)
    {
        $instance = $event->sender;
        if(!($instance instanceof Page))
            return;

        $entry = MailingListEntry::find()->where([
            'page_id' => $instance->id,
        ])->one();
        if($entry)
            $entry->delete();
    }
}
