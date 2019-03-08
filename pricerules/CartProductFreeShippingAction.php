<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Classes\CartRuleActionBase;
use ApplicationException;

class CartProductFreeShippingAction extends CartRuleActionBase
{
    public function getActionType()
    {
        return self::TYPE_CART;
    }

    public function getName()
    {
        return 'Apply free shipping to the cart products';
    }

    /**
     * This method should return true if the action evaluates a 
     * discount value per each product in the shopping cart
     */
    public function isPerProductAction()
    {
        return true;
    }

    /**
     * Evaluates the discount amount. This method should be implemented only for cart-type actions.
     * @param array $params Specifies a list of parameters as an associative array.
     * For example:
     *
     *     ['product' => object, 'shipping_method' => object]
     *
     * @param mixed $hostObj An object to load the action parameters from 
     * @param array $itemDiscountMap A list of cart item identifiers and corresponding discounts.
     * @param array $itemDiscountTaxInclMap A list of cart item identifiers and corresponding discounts with tax included.
     * @param MelonCart\Shop\Classes\RuleConditionBase $productConditions Specifies product conditions to filter the products the discount should be applied to
     * @return float Returns discount value (for cart-wide actions), or a sum of discounts applied to products (for per-product actions) without tax applied
     */
    public function evalDiscount(&$params, $hostObj, &$itemDiscountMap, &$itemDiscountTaxInclMap, $productConditions)
    {
        if (!array_key_exists('cart_items', $params)) {
            throw new ApplicationException('Apply free shipping to the cart products.');
        }

        $cartItems = $params['cart_items'];

        foreach ($cartItems as $item) {
            $originalProductPrice = $item->total_single_price();
            $currentProductPrice = max($originalProductPrice - $itemDiscountMap[$item->key], 0);

            $ruleParams = [];
            $ruleParams['product'] = $item->product;
            $ruleParams['item'] = $item;
            $ruleParams['current_price'] = $item->single_price_no_tax(false) - $item->discount(false);
            $ruleParams['quantity_in_cart'] = $item->quantity;
            $ruleParams['row_total'] = $item->total_price_no_tax();

            $ruleParams['item_discount'] = isset($itemDiscountMap[$item->key]) ? $itemDiscountMap[$item->key] : 0;

            if ($this->isActiveForProduct($item->product, $productConditions, $currentProductPrice, $ruleParams)) {
                $item->free_shipping = true;
            }
        }

        return 0;
    }
}
