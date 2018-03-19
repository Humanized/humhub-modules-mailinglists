<?php

namespace humhub\modules\mailinglists;

use yii\base\Event;
use yii\helpers\Url;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;


class Module extends ContentContainerModule
{
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/mailinglists/admin']);
    }


    public function getContentContainerTypes()
    {
        return [
            Space::className(),
        ];
    }

    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return "Mailing-List";
    }

    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return "Mailing-list to space members";
        }
    }


}


