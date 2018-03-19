<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\db\ActiveRecord;

use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;


/**
 * This is the model class for table "mailing_list_entry".
 *
 * @property integer $id
 * @property integer $page_id
 * @property integer $container_page_id
 * @property bool $is_sent
 */
class MailingListEntry extends ActiveRecord
{
    public static function fromPage($page)
    {
        $entry = new MailingListEntry();
        if($page instanceof ContainerPage)
            $entry->container_page_id = $page->id;
        else
            $entry->page_id = $page->id;
        return $entry;
    }

    public function getPage()
    {
        if($this->container_page_id)
            return $this->hasOne(ContainerPage::className(),
                                 ['id' => 'container_page_id'])->one();
        return $this->hasOne(Page::className(),
                             ['id' => 'page_id'])->one();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_list_entry';
    }
}


