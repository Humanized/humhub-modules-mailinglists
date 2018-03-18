<?php

use yii\db\ActiveRecord;
use humhub\modules\admin\widgets\AdminMenu;

return [
    'id' => 'mailinglists',
    'class' => 'humhub\modules\mailinglists\Module',
    'namespace' => 'humhub\modules\mailinglists',

    'events' => [
        [
            'class' => AdminMenu::className(),
            'event' => AdminMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\mailinglists\Events',
                'onAdminMenuInit'
            ]
        ],
        [
            'class' => ActiveRecord::className(),
            'event' => ActiveRecord::EVENT_AFTER_INSERT,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onTemplateInstanceInsert'
            ],
        ],
        [
            'class' => ActiveRecord::className(),
            'event' => ActiveRecord::EVENT_AFTER_UPDATE,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onTemplateInstanceInsert'
            ],
        ],
        [
            'class' => ActiveRecord::className(),
            'event' => ActiveRecord::EVENT_BEFORE_DELETE,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onTemplateInstanceDelete'
            ],
        ],
    ],
];

?>

