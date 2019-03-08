<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Classes\CartRuleActionBase;
use ApplicationException;

class CartFixedAction extends CartRuleActionBase
{
    public function getActionType()
    {
        return self::TYPE_CART;
    }

    public function getName()
    {
        return 'Discount the shopping cart subtotal by fixed amount';
    }

    public function defineFormFields()
    {
        return 'fields.yaml';
    }

    /**
     * Defines validation rules for the custom fields.
     * @return array
     */
    public function defineValidationRules()
    {
        return [
            'discount_amount' => 'required',
        ];
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
            throw new ApplicationException('Error applying the "Discount the shopping cart subtotal by fixed amount" price rule action: the cart_items element is not found in the action parameters.');
        }

        $includeTax = Shop_CheckoutData::display_prices_incl_tax();
        if (isset($params['no_tax_include']) && $params['no_tax_include']) {
            $includeTax = false;
        }

        /*
         * This parameter is used only for the manual order discount setting feature
         */
        $cartItems = $params['cart_items'];
        $totalDiscount = 0;
        $totalDiscountInclTax = 0;
        $remainder = $hostObj->discount_amount;

        foreach ($cartItems as $item) {
            $originalProductPrice = $item->total_single_price();
            $currentProductPrice = max($originalProductPrice - $itemDiscountMap[$item->key], 0);

            $ruleParams['product'] = $item->product;
            $ruleParams['item'] = $item;
            $ruleParams['current_price'] = $item->single_price_no_tax(false) - $item->discount(false);
            $ruleParams['quantity_in_cart'] = $item->quantity;
            $ruleParams['row_total'] = $item->total_price_no_tax();

            if ($this->isActiveForProduct($item->product, $productConditions, $currentProductPrice, $ruleParams)) {
                $discountValue = $remainder/$item->quantity;

                /*
                 * If taxes are included to discounts, we need to extract the real discount value from the discount total
                 */
                if ($includeTax) {
                    $discountValue = Shop_TaxClass::get_subtotal($item->product->tax_class_id, $discountValue);
                }

                if ($discountValue > $currentProductPrice) {
                    $totalDiscountInclTax = $discountValue = $currentProductPrice;
                }

                if ($includeTax) {
                    $totalDiscountInclTax = Shop_TaxClass::get_total_tax($item->product->tax_class_id, $discountValue) + $discountValue;
                }

                $totalDiscount += $discountValue*$item->quantity;
                $itemDiscountMap[$item->key] += $discountValue;
                $itemDiscountTaxInclMap[$item->key] += $totalDiscountInclTax;

                if (!$includeTax) {
                    $remainder -= $discountValue*$item->quantity;
                }
                else {
                    $remainder -= $totalDiscountInclTax*$item->quantity;
                }
            }

            if ($remainder <= 0) {
                break;
            }
        }

        return $totalDiscount;
    }
}
