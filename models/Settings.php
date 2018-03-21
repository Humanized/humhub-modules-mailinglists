<?php

namespace humhub\modules\mailinglists\models;

use humhub\modules\custom_pages\modules\template\models\Template;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *  Module settings. Also be used for as a model form.
 */
class Settings extends Model
{
    public $settings;
    public $space;

    public $mailTemplate;
    /**
     *  Email body (when page content is not included directly)
     */
    public $mailBody;
    /**
     *  Unsubscribe/Legal mention
     */
    public $mailMention;

    public $defaultBody =
        "<p>" .
        "We just published our last informational mail. Why don't you " .
        'come out and take a look? <a href="{{ page.url }}">{{ page.url }}</a>?' .
        "</p>"
         ;
    public $defaultMention =
        '--<p style="font-size: 0.9em">' .
        'You can unsubscribe from this mailing-list following this link: ' .
        '<a href="{{ member.unsubscribe }}">{{ member.unsubscribe }}</a>' .
        '</p>'
    ;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $settings = $this->getSettings();
        $gsettings = $this->globals();

        $this->mailTemplate = $settings->get('mailTemplate',
            isset($gsettings->mailTemplate) ? $gsettings->mailTemplate : 0
        );
        // only global
        $this->mailBody = $gsettings()->get('mailBody', $this->defaultBody);
        $this->mailMention = $gsettings()->get('mailMention', $this->defaultMention);
    }


    /**
     *  Return a list of all templates usable in dropdown menu
     */
    public function getTemplates()
    {
        return ArrayHelper::map(
            Template::find()->select(['id','name'])->asArray()->all(),
            'id', 'name'
        );
    }

    /**
     *  Return global settings
     */
    function globals()
    {
        return Yii::$app->getModule('mailinglists')->settings;
    }

    /**
     *  Return app/module settings depending on $this->space
     */
    function getSettings()
    {
        if(!$this->settings) {
            $module = Yii::$app->getModule('mailinglists');
            $this->settings = ($this->space) ?
                $module->settings->contentContainer($this->space) :
                $module->settings;
        }
        return $this->settings;
    }

    /**
     *  Update a given module setting using this instance
     *  attributes.
     */
    function update($name, $defaultValue)
    {
        $settings = $this->getSettings();
        if(empty($this->$name)) {
            $settings->delete($name);
            $this->$name = $defaultValue;
        } else {
            $settings->set($name, $this->$name);
        }
    }

    /**
     * Saves the settings in case the validation succeeds.
     */
    public function save()
    {
        if(!$this->validate()) {
            return false;
        }
        $this->update('mailTemplate', 0);
        if(!$this->space) {
            $this->update('mailBody', $this->defaultBody);
            $this->update('mailMention', $this->defaultMention);
        }
        return true;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mailTemplate', 'integer'],
            ['mailBody', 'string'],
            ['mailMention', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mailBody' => 'Emails Body',
            'mailMention' => 'Legal Mention',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'mailBody' => 'body of emails when they don\'t include the page contnt',
            'mailMention' => 'legal mention such as unsubscribe etc.',
        ];
    }
}
