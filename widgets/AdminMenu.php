<?php

namespace humhub\modules\mailinglists\widgets;

use Yii;
use yii\helpers\Url;
use humhub\widgets\BaseMenu;


/**
 * User Administration Menu.
 * Adapted for Mailinglists by bkfox
 *
 * @author Basti
 */
class AdminMenu extends BaseMenu
{

    public $template = "@humhub/widgets/views/tabMenu";
    public $type = "";

    static function isActive($action = null)
    {
        $controller = Yii::$app->controller;
        return (
            $controller->module &&
            $controller->module->id == 'mailinglists' &&
            $controller->id == 'admin' &&
            $controller->action->id == $action
        );
    }

    public function init()
    {
        $this->addItem([
            'label' => "Mails",
            'url' => Url::to(['/mailinglists/admin']),
            'sortOrder' => 100,
            'isActive' => AdminMenu::isActive('index') || AdminMenu::isActive('entries') || AdminMenu::isActive('send')
        ]);

        $this->addItem([
            'label' => "Settings",
            'url' => Url::to(['/mailinglists/admin/settings']),
            'sortOrder' => 100,
            'isActive' => AdminMenu::isActive('settings')
        ]);

        parent::init();
    }

}
