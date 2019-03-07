<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Cart Rules Back-end Controller
 */
class CartRules extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \MelonCart\Shop\Behaviors\RuleListController::class,
    ];

    public $formConfig = 'config_form.yaml';

    public $rulesModelClass = \MelonCart\Shop\Models\CartPriceRule::class;
    public $rulesUpdateUrl = 'meloncart/shop/cartrules/update';

    public $requiredPermissions = ['meloncart.shop.manage_discounts'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('MelonCart.Shop', 'shop', 'discounts');
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
