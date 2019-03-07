<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Catalog Rules Back-end Controller
 */
class CatalogRules extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \MelonCart\Shop\Behaviors\RuleListController::class,
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['meloncart.shop.manage_discounts'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('MelonCart.Shop', 'shop', 'rules');
    }

    public function index()
    {
        $this->bodyClass = 'slim-container';
    }

    // public function create($context = null)
    // {
    //     $this->asExtension('FormController')->create($context);

    //     if ($model = $this->formGetModel()) {
    //         $model->initConditions($this->formGetSessionKey());
    //     }
    // }
}
