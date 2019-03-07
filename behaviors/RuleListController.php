<?php namespace MelonCart\Shop\Behaviors;

use MelonCart\Shop\Models\CatalogPriceRule;
use Backend\Classes\ControllerBehavior;
use ApplicationException;
use Exception;

/**
 * Rule List Behavior
 *
 * Adds features for displaying rule lists
 */
class RuleListController extends ControllerBehavior
{
    use \Backend\Traits\CollapsableWidget;

    public $rulesModelClass = CatalogPriceRule::class;
    public $rulesUpdateUrl = 'meloncart/shop/catalogrules/update';

    /**
     * Behavior constructor
     * @param Backend\Classes\Controller $controller
     */
    public function __construct($controller)
    {
        parent::__construct($controller);

        $this->addJs('js/rules.js', 'MelonCart.Shop');
        $this->addCss('css/rules.css', 'MelonCart.Shop');
    }

    public function rulesRender()
    {
        $rules = $this->createModel()->get();

        $this->vars['rules'] = $rules;

        return $this->makePartial('rules_container');
    }

    /**
     * Controller accessor for making partials within this behavior.
     * @param string $partial
     * @param array $params
     * @return string Partial contents
     */
    public function rulesMakePartial($partial, $params = [])
    {
        return $this->makePartial($partial, $params);
    }

    /**
     * Internal method, prepare the form model object
     * @return Model
     */
    protected function createModel()
    {
        $class = $this->controller->rulesModelClass;
        $model = new $class;
        return $model;
    }

    public function rulesGetCollapseStatus($rule)
    {
        return $this->getCollapseStatus($rule->id, false);
    }
}
