<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

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
    public $globalTemplate;
    public $globalSignature;

    public $defaultSignature =
        '<hr><small>unsubscribe shit</small>'
    ;

    /**
     *  Return a list of available templates as [ $id => $name ]
     */
    public function getTemplates() {
        return ArrayHelper::map(
            Template::find()->select(['id','name'])->asArray()->all(),
            'id', 'name'
        );
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->settings = Yii::$app->getModule('mailinglists')->settings;
        $this->globalTemplate = intval($this->settings->get('globalTemplate', 0));
        $this->globalSignature = $this->settings->get(
            'globalSignature', $this->defaultSignature
        );
    }

    /**
     *  Update a given module setting using this instance
     *  attributes.
     */
    function updateSetting($name, $defaultValue)
    {
        if(empty($this->$name)) {
            $this->settings->delete($name);
            $this->$name = $defaultValue;
        } else {
            $this->settings->set($name, $this->$name);
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
        $this->updateSetting('globalTemplate', 0);
        $this->updateSetting('globalSignature', $this->defaultSignature);
        return true;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['globalTemplate', 'integer'],
            ['globalSignature', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'globalTemplate' => 'Template',
            'globalSignature' => 'Signature',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'globalTemplate' => 'This template will be used for mails.',
            'globalSignature' => ''
        ];
    }
}
