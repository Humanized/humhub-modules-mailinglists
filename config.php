<?php

use yii\db\ActiveRecord;

return [
    'id' => 'mailinglist',
    'class' => 'humhub\modules\mailinglist\Module',
    'namespace' => 'humhub\modules\mailinglist',

    'events' => [
        [
            'class' => ActiveRecord::className(),
            'event' => ActiveRecord::EVENT_AFTER_UPDATE,
            'callback' => [
                  'humhub\modules\mailinglist\Events',
                  'onTemplateInstanceInsert'
            ]
        ],
    ],
];

?>

