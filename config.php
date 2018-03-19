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
            'class' => '\humhub\modules\custom_pages\models\Page',
            'event' => ActiveRecord::EVENT_AFTER_INSERT,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onPageInsert'
            ],
        ],
        [
            'class' => '\humhub\modules\custom_pages\models\Page',
            'event' => ActiveRecord::EVENT_AFTER_UPDATE,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onPageInsert'
            ],
        ],
        [
            'class' => '\humhub\modules\custom_pages\models\Page',
            'event' => ActiveRecord::EVENT_BEFORE_DELETE,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onPageDelete'
            ],
        ],
    ],
];

?>

