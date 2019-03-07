<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Backend\Widgets\Form;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('MelonCart.Shop', 'meloncart', 'products');
    }

    // public function create()
    // {
    //     $this->asExtension('FormController')->create();
    //     $this->formGetWidget()->getFormWidget('optionsList')->getGrid()->bindEvent('grid.dataChanged', [$this, 'optionsListDataChanged']);
    // }

    // public function update($recordId = null, $context = null)
    // {
    //     $this->asExtension('FormController')->update($recordId, $context);
    //     $this->formGetWidget()->getFormWidget('optionsList')->getGrid()->bindEvent('grid.dataChanged', [$this, 'optionsListDataChanged']);
    // }

    // public function optionsListDataChanged(array $changes)
    // {
    //     dd($changes);
    // }
}
