<?php namespace MelonCart\Shop\Classes;

use System\Classes\PluginManager;
use October\Rain\Extension\ExtensionBase;

/**
 * RuleCompoundConditionBase Class
 */
class RuleActionBase extends ExtensionBase
{
    use \System\Traits\ConfigMaker;

    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';

    protected static $actionClasses = null;

    /**
     * This function should return one of the 'product' or 'cart' words,
     * depending on a place where the action is valid
     *
     * @return string
     */
    public function getActionType()
    {
        return self::TYPE_PRODUCT;
    }

    /**
     * Extra field configuration for the condition.
     */
    public function defineFormFields()
    {
        return 'fields.yaml';
    }

    /**
     * Initializes configuration data when the condition is first created.
     * @param  Model $host
     */
    public function initConfigData($host){}

    /**
     * Defines validation rules for the custom fields.
     * @return array
     */
    public function defineValidationRules()
    {
        return [];
    }

    /**
     * Returns an action name for displaying in the action selection drop-down menu
     *
     * @return string
     */
    public function getName()
    {
        return 'Action';
    }

    /**
     * Spins over types registered in plugin base class with `registerShopPriceRules`,
     * checks if the action type matches and adds it to an array that is returned.
     *
     * @param string $type Use `TYPE_*` constants
     * @return array
     */
    public static function findActionsByType($type)
    {
        $results = [];
        $bundles = PluginManager::instance()->getRegistrationMethodValues('registerShopPriceRules');

        foreach ($bundles as $plugin => $bundle) {
            foreach ((array) array_get($bundle, 'actions', []) as $actionClass) {
                if (!class_exists($actionClass)) {
                    continue;
                }

                $obj = new $actionClass;
                if ($obj->getActionType() != $type) {
                    continue;
                }

                $results[$actionClass] = $obj;
            }
        }


        return $results;
    }
}
