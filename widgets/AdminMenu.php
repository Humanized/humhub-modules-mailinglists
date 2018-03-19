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
    public $space = null;
    public $template = "@humhub/widgets/views/tabMenu";
    public $type = "";

    static function isActive($action = null)
    {
        $controller = Yii::$app->controller;
        return (
            $controller->module &&
            $controller->module->id == 'mailinglists' &&
            ($controller->id == 'admin' || $controller->id == 'container') &&
            $controller->action->id == $action
        );
    }

    public function init()
    {
        $space = $this->space;
        $this->addItem([
            'label' => "Mails",
            'url' => ($space) ?
                $space->createUrl('container/'):
                Url::to(['admin/']),
            'sortOrder' => 100,
            'isActive' => AdminMenu::isActive('index') ||
                          AdminMenu::isActive('entries') ||
                          AdminMenu::isActive('send')
        ]);

        $this->addItem([
            'label' => "Settings",
            'url' => ($space) ?
                $space->createUrl('container/settings'):
                Url::to(['admin/settings']),
            'sortOrder' => 100,
            'isActive' => AdminMenu::isActive('settings')
        ]);

        parent::init();
    }

}
