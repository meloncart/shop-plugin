<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Classes\RuleActionBase;

class ProductToFixedPriceAction extends RuleActionBase
{
    public function getActionType()
    {
        return self::TYPE_PRODUCT;
    }

    public function getName()
    {
        return 'To fixed price';
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
     * Evaluates the product price (for product-type actions) or discount amount (for cart-type actions)
     * @param array $params Specifies a list of parameters as an associative array.
     * For example:
     *
     *     ['product' => object, 'shipping_method' => object]
     *
     * @param mixed $hostObj An object to load the action parameters from 
     */
    public function evalAmount(&$params, $hostObj)
    {
        return $hostObj->discount_amount;
    }
}
