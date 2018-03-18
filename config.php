<?php

use yii\db\ActiveRecord;

return [
    'id' => 'mailinglists',
    'class' => 'humhub\modules\mailinglists\Module',
    'namespace' => 'humhub\modules\mailinglists',

    'events' => [
        [
            'class' => ActiveRecord::className(),
            'event' => ActiveRecord::EVENT_AFTER_UPDATE,
            'callback' => [
                  'humhub\modules\mailinglists\Events',
                  'onTemplateInstanceInsert'
            ]
        ],
    ],
];

?>

