<?php namespace MelonCart\Shop\Classes;

use RainLab\Notify\Classes\ConditionBase;
use RainLab\Notify\Interfaces\Condition as ConditionInterface;

/**
 * Rule Condition Base Class
 */
class RuleConditionBase extends ConditionBase implements ConditionInterface
{
    const TYPE_ANY = 'any';
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_CART_ROOT = 'cart-root';
    const TYPE_CART_ATTRIBUTE = 'cart-attribute';
    const TYPE_CART_PRODUCT_ATTRIBUTE = 'cart-product-attribute';

    /**
     * @var string The plugin class method used to look for conditions.
     */
    protected static $registrationMethod = 'registerShopPriceRules';
}
