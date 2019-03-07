<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use RainLab\User\Models\User;
use MelonCart\Shop\Models\Customer;

/**
 * Customers Back-end Controller
 */
class Customers extends Controller
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

        BackendMenu::setContext('MelonCart.Shop', 'meloncart', 'customers');
    }

    /**
     * Controller override: Extend the query used for populating the list
     * after the default query is processed.
     * @param October\Rain\Database\Builder $query
     */
    public function listExtendQuery($query, $definition = null)
    {
        $customers_table =(new Customer)->gettable();
        $users_table = (new User)->gettable();
        $query->leftJoin($users_table, $users_table.'.id', '=', $customers_table.'.user_id');
    }
}