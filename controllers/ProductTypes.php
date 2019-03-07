<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * ProductTypes Back-end Controller
 */
class ProductTypes extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('MelonCart.Shop', 'meloncart', 'products');
    }
}
