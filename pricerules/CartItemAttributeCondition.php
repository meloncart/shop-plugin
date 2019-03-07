<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Classes\RuleConditionBase;
use RainLab\Notify\Classes\ModelAttributesConditionBase;
use ApplicationException;

class CartItemAttributeCondition extends ModelAttributesConditionBase
{
    protected $modelClass = \MelonCart\Shop\Models\CartItem::class;

    public function getConditionType()
    {
        return RuleConditionBase::TYPE_CART_PRODUCT_ATTRIBUTE;
    }

    public function getGroupingTitle()
    {
        return 'Shopping cart item attribute';
    }

    public function getTitle()
    {
        return 'Shopping cart item attribute';
    }

    public function getValueControlType()
    {
        $attribute = $this->host->subcondition;

        if ($attribute == 'bundle_item') {
            return 'dropdown';
        }

        return parent::getValueControlType();
    }

    public function getValueDropdownOptions()
    {
        $attribute = $this->host->subcondition;

        if ($attribute == 'bundle_item') {
            return [
                'false' => 'FALSE (product IS NOT a bundle item)',
                'true' => 'TRUE (product IS a bundle item)'
            ];
        }

        return parent::getValueDropdownOptions();
    }

    public function prepareReferenceListInfo()
    {
        if (!is_null($this->referenceInfo)) {
            return $this->referenceInfo;
        }

        $attribute = $this->host->subcondition;

        if ($attribute == 'bundle_item') {
            return null;
        }

        return parent::prepareReferenceListInfo();
    }

    public function getCustomTextValue()
    {
        $attribute = $this->host->subcondition;

        if ($attribute == 'bundle_item') {
            return $this->host->value == 'true' ? 'TRUE' : 'FALSE';
        }

        return false;
    }

    public function getOperatorOptions()
    {
        $hostObj = $this->host;
        $attribute = $hostObj->subcondition;

        if ($attribute == 'bundle_item') {
            return ['is' => 'is'];
        }

        return parent::getOperatorOptions();
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

        $attribute = $hostObj->subcondition;

        if ($attribute == 'price') {
            if (!array_key_exists('current_price', $params)) {
                throw new ApplicationException('Error evaluating the cart item attribute condition: the current_price element is not found in the condition parameters.');
            }

            return parent::evalIsTrue(null, $params['current_price']);
        }

        if ($attribute == 'row_total') {
            if (!array_key_exists('row_total', $params)) {
                throw new ApplicationException('Error evaluating the cart item attribute condition: the row_total element is not found in the condition parameters.');
            }

            return parent::evalIsTrue(null, $params['row_total']);
        }

        if ($attribute == 'quantity') {
            if (!array_key_exists('quantity_in_cart', $params)) {
                throw new ApplicationException('Error evaluating the cart item attribute condition: the quantity_in_cart element is not found in the condition parameters.');
            }

            return parent::evalIsTrue(null, $params['quantity_in_cart']);
        }

        if ($attribute == 'discount') {
            if (!array_key_exists('item_discount', $params)) {
                throw new ApplicationException('Error evaluating the cart item attribute condition: the item_discount element is not found in the condition parameters.');
            }

            return parent::evalIsTrue(null, $params['item_discount']);
        }

        if ($attribute == 'bundle_item') {
            if (!array_key_exists('item', $params)) {
                throw new ApplicationException('Error evaluating the cart item attribute condition: the item element is not found in the condition parameters.');
            }

            $isBundleItem = $params['item']->isBundleItem();

            if ($hostObj->value == 'false') {
                return !$isBundleItem;
            }
            else {
                return $isBundleItem;
            }
        }

        return false;
    }
}
