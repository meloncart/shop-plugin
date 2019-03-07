<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use MelonCart\Shop\Models\Category;

/**
 * Categories Back-end Controller
 */
class Categories extends Controller
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

        BackendMenu::setContext('MelonCart.Shop', 'meloncart', 'categories');
    }

    /**
     * Displays the menu items in a tree list view so they can be reordered
     */
    public function reorder()
    {
        $this->pageTitle = 'Reorder Categories';

        $toolbarConfig          = $this->makeConfig();
        $toolbarConfig->buttons = '$/meloncart/shop/controllers/categories/_reorder_toolbar.htm';

        $this->vars['toolbar'] = $this->makeWidget('Backend\Widgets\Toolbar', $toolbarConfig);
        $this->vars['records'] = Category::make()->getEagerRoot();
    }

    /**
     * Update the menu item position
     */
    public function reorder_onMove()
    {
        $sourceNode = Category::find(post('sourceNode'));
        $targetNode = post('targetNode') ? Category::find(post('targetNode')) : null;

        if ($sourceNode == $targetNode) {
            return;
        }

        switch (post('position')) {
            case 'before':
                $sourceNode->moveBefore($targetNode);
                break;
            case 'after':
                $sourceNode->moveAfter($targetNode);
                break;
            case 'child':
                $sourceNode->makeChildOf($targetNode);
                break;
            default:
                $sourceNode->makeRoot();
                break;
        }
    }
}
