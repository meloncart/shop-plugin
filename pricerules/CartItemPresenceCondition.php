<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Classes\RuleCompoundCondition;
use MelonCart\Shop\Classes\RuleConditionBase;
use ApplicationException;

class CartItemPresenceCondition extends RuleCompoundCondition
{
    public function getConditionType()
    {
        return RuleConditionBase::TYPE_CART_ROOT;
    }

    public function getAllowedSubtypes()
    {
        return [
            RuleConditionBase::TYPE_PRODUCT,
            RuleConditionBase::TYPE_CART_PRODUCT_ATTRIBUTE
        ];
    }

    public function getTitle()
    {
        return 'Shopping cart item presence condition';
    }

    /**
     * Returns a condition name for displaying in the condition selection drop-down menu
     */
    public function getName()
    {
        return 'Item is present/not present in the shopping cart';
    }

    public function getJoinText()
    {
        return $this->host->condition_type == 0 ? 'AND' : 'OR';
    }

    public function getText()
    {
        $result = 'Item with ';

        if ($this->host->condition_type == 0) {
            $result .= ' ALL ';
        }
        else {
            $result .= ' ANY ';
        }

        $result .= ' of subconditions ';

        if ($this->host->presence == 'found') {
            $result .= 'SHOULD BE';
        }
        else {
            $result .= 'SHOULD NOT BE';
        }

        $result .= ' presented in the shopping cart';

        return $result;
    }

    public function defineFormFields()
    {
        return 'fields.yaml';
    }

    public function initConfigData($host)
    {
        $host->presence = 'found';
        $host->condition_type = 0;
    }

    public function getPresenceOptions()
    {
        return [
            'found' => 'Item is FOUND in the shopping cart',
            'not_found' => 'Item is NOT FOUND in the shopping cart',
        ];
    }

    public function getConditionTypeOptions()
    {
        $options = [
            '0' => 'ALL of subconditions should be TRUE',
            '1' => 'ANY of subconditions should be TRUE'
        ];

        return $options;
    }

    /**
     * Checks whether the condition is TRUE for specified parameters
     * @param array $params Specifies a list of parameters as an associative array.
     * For example:
     *
     *     ['product' => object, 'shipping_method' => object]
     *
     * @return bool
     */
    public function isTrue(&$params)
    {
        $hostObj = $this->host;

        if (!array_key_exists('cart_items', $params)) {
            throw new ApplicationException('Error evaluating the cart item presence condition: the cart_items element is not found in the condition parameters.');
        }

        $requiredConditionValue = true;
        $matches_found = null;
        $localMatch = false;

        foreach ($params['cart_items'] as $item) {
            $params['product'] = $item->product;
            $params['item'] = $item;
            $params['current_price'] = $item->single_price_no_tax(false) - $item->discount(false);
            $params['quantity_in_cart'] = $item->quantity;
            $params['row_total'] = $item->total_price_no_tax();

            if (method_exists($item, 'get_om_record')) {
                $params['om_record'] = $item->get_om_record();
            }

            $subconditionsResult = null;
            $result_found = false;

            foreach ($hostObj->children as $subcondition) {
                $subconditionResult = $subcondition->isTrue($params)  ? true : false;

                /*
                 * All
                 */
                if ($hostObj->condition_type == 0) {
                    if ($subconditionResult !== $requiredConditionValue) {
                        $subconditionsResult = false;
                        break;
                    }
                }
                /*
                 * Any
                 */
                else {
                    if ($subconditionResult === $requiredConditionValue && $hostObj->presence == 'found') {
                        return true;
                    }
                }

                if ($subconditionResult === $requiredConditionValue) {
                    $localMatch = true;

                    if ($subconditionsResult !== null) {
                        $subconditionsResult = $subconditionsResult && true;
                    }
                    else {
                        $subconditionsResult = true;
                    }
                }
                else {
                    $subconditionsResult = false;
                }
            }

            if ($hostObj->condition_type == 0 && $hostObj->presence == 'found' && $subconditionsResult) {
                return true;
            }

            if ($subconditionsResult) {
                $matches_found = true;
            }
        }

        if ($hostObj->condition_type == 0 && $hostObj->presence == 'not_found' && !$matches_found) {
            return true;
        }

        if ($hostObj->condition_type == 1 && $hostObj->presence == 'not_found' && !$localMatch) {
            return true;
        }

        return false;
    }
}
