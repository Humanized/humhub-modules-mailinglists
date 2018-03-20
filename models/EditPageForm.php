<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;


class EditPageForm extends Model
{
    public $entry;
    public $subject;

    public function rules()
    {
        return [
            ['entry', 'integer'],
            ['subject', 'string'],
        ];
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        $entry = MailingListEntry::findOne($this->entry);
        $page = $entry->page;
        $page->title = $this->subject;
        $page->save();
        return true;
    }

}



