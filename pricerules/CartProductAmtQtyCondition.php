<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Classes\RuleCompoundCondition;
use MelonCart\Shop\Classes\RuleConditionBase;
use ApplicationException;

class CartProductAmtQtyCondition extends RuleCompoundCondition
{
    protected $operators = [
        'is' => 'is',
        'is_not' => 'is not',
        'equals_or_greater' => 'equals or greater than',
        'equals_or_less' => 'equals or less than',
        'greater' => 'greater than',
        'less' => 'less than'
    ];

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

    /**
     * Returns a condition name for displaying in the condition selection drop-down menu
     */
    public function getName()
    {
        return 'Cart items total quantity or total amount';
    }

    public function getTitle()
    {
        return 'Cart item quantity or total amount condition';
    }

    public function getJoinText()
    {
        return $this->host->condition_type == 0 ? 'AND' : 'OR';
    }

    public function getText()
    {
        $host = $this->host;

        if ($host->parameter == 'quantity') {
            $result = 'Total quantity ';
        }
        else {
            $result = 'Total amount ';
        }

        if (array_key_exists($host->operator, $this->operators)) {
            $result .= $this->operators[$host->operator].' ';
        }
        else {
            $result .= ' is ';
        }

        $result .= strlen($host->value) ? $host->value : 0;
        $result .= ' for cart items matching';

        if ($host->condition_type == 0) {
            $result .= ' ALL ';
        }
        else {
            $result .= ' ANY ';
        }

        $result .= ' of subconditions';

        return $result;
    }

    public function defineFormFields()
    {
        return 'fields.yaml';
    }

    public function initConfigData($host)
    {
        $host->parameter = 'quantity';
        $host->operator = 'is';
        $host->condition_type = 0;
        $host->value = 0;
    }

    public function defineValidationRules()
    {
        return [
            'value' => 'required'
        ];
    }

    public function getOperatorOptions()
    {
        return $this->operators;
    }

    public function getParameterOptions()
    {
        $options = [
            'quantity' => 'Total quantity',
            'amount' => 'Total amount'
        ];

        return $options;
    }

    public function getConditionTypeOptions()
    {
        $options = [
            '0' => 'Cart items should match ALL subconditions',
            '1' => 'Cart items should match ANY subconditions'
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
            throw new ApplicationException('Error evaluating the cart items total quantity or total amount condition: the cart_items element is not found in the condition parameters.');
        }

        $requiredConditionValue = true;

        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($params['cart_items'] as $item) {
            $params['product'] = $item->product;
            $params['item'] = $item;

            $itemCurrentPrice = $item->get_de_cache_item('current_price');
            if ($itemCurrentPrice === false) {
                $itemCurrentPrice = $item->set_de_cache_item('current_price', $item->single_price_no_tax() - $item->discount(false));
            }

            $params['current_price'] = $itemCurrentPrice;
            $params['quantity_in_cart'] = $item->quantity;

            $itemRowTotal = $item->get_de_cache_item('row_total');
            if ($itemRowTotal === false) {
                $itemRowTotal = $item->set_de_cache_item('row_total', $item->total_price_no_tax());
            }

            $rowTotal = $params['row_total'] = $itemRowTotal;

            if (method_exists($item, 'get_om_record')) {
                $params['om_record'] = $item->get_om_record();
            }

            $subconditionsResult = null;
            if (!$hostObj->children->count()) {
                $subconditionsResult = true;
            }

            foreach ($hostObj->children as $subcondition) {
                $subconditionResult = $subcondition->isTrue($params)  ? true : false;

                /*
                 * All
                 */
                if ($hostObj->condition_type == 0) {
                    if ($subconditionResult !== $requiredConditionValue) {
                        continue 2;
                    }
                }
                /*
                 * Any
                 */
                else {
                    if ($subconditionResult === $requiredConditionValue) {
                        $subconditionsResult = true;
                        break;
                    }
                }

                if ($subconditionResult === $requiredConditionValue) {
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

            if ($subconditionsResult) {
                $totalQuantity += $item->quantity;
                $totalAmount += $rowTotal;
            }
        }

        $testValue = $hostObj->parameter == 'quantity' ? $totalQuantity : $totalAmount;
        $conditionValue = $hostObj->value;
        $operator = $hostObj->operator;

        if ($operator == 'is') {
            return $testValue == $conditionValue;
        }

        if ($operator == 'is_not') {
            return $testValue != $conditionValue;
        }

        if ($operator == 'equals_or_greater') {
            return $testValue >= $conditionValue;
        }

        if ($operator == 'equals_or_less') {
            return $testValue <= $conditionValue;
        }

        if ($operator == 'greater') {
            return $testValue > $conditionValue;
        }

        if ($operator == 'less') {
            return $testValue < $conditionValue;
        }

        return false;
    }
}
