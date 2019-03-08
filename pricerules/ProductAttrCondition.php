<?php namespace MelonCart\Shop\PriceRules;

use MelonCart\Shop\Models\ProductProperty;
use MelonCart\Shop\Classes\RuleConditionBase;
use RainLab\Notify\Classes\ModelAttributesConditionBase;

class ProductAttrCondition extends ModelAttributesConditionBase
{
    protected $modelClass = \MelonCart\Shop\Models\Product::class;

    protected function getConditionTextPrefix($parametersHost, $attributes)
    {
        if ($parametersHost->subcondition != 'product') {
            return 'Product.'.$attributes[$parametersHost->subcondition];
        }

        return 'Product';
    }

    public function getGroupingTitle()
    {
        return 'Product ATTR';
    }

    public function getTitle()
    {
        return 'Product ATTR condition';
    }

    public function getCustomTextValue()
    {
        return false;
    }

    public function getOperatorOptions()
    {
        $options = [
            'is' => 'is',
            'equals_or_greater' => 'equals_or_greater',
            'equals_or_less' => 'equals_or_less',
            'greater' => 'greater',
            'less' => 'less'
        ];

        return $options;
    }

    public function getConditionType()
    {
        return RuleConditionBase::TYPE_ANY;
    }

    public function getValueDropdownOptions()
    {
        $attribute = $this->host->subcondition;

        return parent::getValueDropdownOptions();
    }

    public function prepareReferenceListInfo()
    {
        if (!is_null($this->referenceInfo)) {
            return $this->referenceInfo;
        }

        $attribute = $this->host->subcondition;

        $this->referenceInfo = [];
        $this->referenceInfo['referenceModel'] = new ProductProperty;
        $this->referenceInfo['columns'] = ['name'];

        return $this->referenceInfo = (object) $this->referenceInfo;
    }

    protected function listModelAttributes()
    {
        if (Phpr::$config->get('DISABLE_ATTR_CONDITIONS', false)) {
            return [];
        }

        if ($this->modelAttributes) {
            return $this->modelAttributes;
        }

        $query = "
            select 
                    *
                from shop_product_properties
            ";

        $re = Db_DbHelper::queryArray($query);

        $attributes = [];
        foreach ($re as $r) {
            $attributes['attr_' . $r['name']] = 'Product ATTR: ' . $r['name'];
        }

        asort($attributes);

        return $this->modelAttributes = $attributes;
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

        /*
         * This is for CART CONDITION
         */
        if (array_key_exists('cart_items', $params)) {
            $attribute = $hostObj->subcondition;
            $attribute = preg_replace('/^attr_/', '', $attribute);

            foreach ($params['cart_items'] as $item) {
                $operator = $hostObj->operator;
                $conditionValue = $hostObj->value;
                $val = $item->product->get_attribute($attribute);

                if ($operator == 'is') {
                    if ($val == $conditionValue) {
                        return true;
                    }
                }

                if ($operator == 'equals_or_greater') {
                    if ($val >= $conditionValue) {
                        return true;
                    }
                }

                if ($operator == 'equals_or_less') {
                    if ($val <= $conditionValue) {
                        return true;
                    }
                }

                if ($operator == 'greater') {
                    if ($val > $conditionValue) {
                        return true;
                    }
                }

                if ($operator == 'less') {
                    if ($val < $conditionValue) {
                        return true;
                    }
                }
            }

            return false;
        }
        /*
         * This is for PRODUCT FILTER
         */
        elseif (array_key_exists('product', $params)) {
            $product = $params['product'];
            $attribute = $hostObj->subcondition;
            $attribute = preg_replace('/^attr_/', '', $attribute);

            $operator = $hostObj->operator;
            $conditionValue = $hostObj->value;
            $val = $product->get_attribute($attribute);

            if ($operator == 'is') {
                if ($val == $conditionValue) {
                    return true;
                }
            }

            if ($operator == 'equals_or_greater') {
                if ($val >= $conditionValue) {
                    return true;
                }
            }

            if ($operator == 'equals_or_less') {
                if ($val <= $conditionValue) {
                    return true;
                }
            }

            if ($operator == 'greater') {
                if ($val > $conditionValue) {
                    return true;
                }
            }

            if ($operator == 'less') {
                if ($val < $conditionValue) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
}
