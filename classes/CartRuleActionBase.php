<?php namespace MelonCart\Shop\Classes;

/**
 * CartRuleActionBase Class
 */
class CartRuleActionBase extends RuleActionBase
{
    /**
     * This method should return true if the action evaluates a 
     * discount value per each product in the shopping cart
     */
    public function isPerProductAction()
    {
        return false;
    }

    /**
     * Checks whether action should be applied to a specified product. This method should be used
     * by inherited actions for filtering products basing on the conditions specified on the Action tab
     * of a Cart Price Rule
     */
    protected function isActiveForProduct(
        $product,
        $productConditions,
        $currentProductPrice,
        $ruleParams = [],
        $item = null
    )
    {
        if ($productConditions === null) {
            return true;
        }

        $params = ['product' => $product, 'current_price' => $currentProductPrice];

        if (array_key_exists('item', $ruleParams) && method_exists($ruleParams['item'], 'getOmRecord')) {
            $params['om_record'] = $ruleParams['item']->getOmRecord();
        }

        foreach ($ruleParams as $key => $value) {
            $params[$key] = $value;
        }

        $result = $productConditions->isTrue($params);

        return $result;
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
     * The row totals can be changed by per-product actions.
     * @param array $itemDiscountTaxInclMap A list of cart item identifiers and corresponding discounts with tax included.
     * @param RuleConditionBase $productConditions Specifies product conditions to filter the products the discount should be applied to
     * @return float Returns discount value (for cart-wide actions), or a sum of discounts applied to products (for per-product actions) without tax applied
     */
    public function evalDiscount(
        &$params,
        $hostObj,
        &$itemDiscountMap,
        &$itemDiscountTaxInclMap,
        $productConditions
    )
    {
        return null;
    }

}
