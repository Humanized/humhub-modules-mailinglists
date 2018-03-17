<?php

use yii\db\ActiveRecord;

return [
    'id' => 'mailing_lists',
    'class' => 'humhub\modules\mailing_lists\Module',
    'namespace' => 'humhub\modules\mailing_lists',

    'events' => [
        [
            'class' => ActiveRecord::className(),
            'event' => ActiveRecord::EVENT_AFTER_UPDATE,
            'callback' => [
                  'humhub\modules\mailing_lists\Events',
                  'onTemplateInstanceInsert'
            ]
        ],
    ],
];

?>

