<?php

namespace humhub\modules\mailing_lists;

use yii\base\Event;
use yii\helpers\Url;
use humhub\modules\content\components\ContentContainerModule;

use humhub\modules\custom_pages\modules\template\models\TemplateInstance;


use humhub\modules\mailing_lists\Events;

class Module extends ContentContainerModule
{
    public function init()
    {
        parent::init();

        Event::on(
            TemplateInstance::className(),
            TemplateInstance::EVENT_AFTER_UPDATE,
            function($e) { Events::onTemplateInstanceInsert($e); }
        );
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/mailing_lists/admin']);
    }
}


