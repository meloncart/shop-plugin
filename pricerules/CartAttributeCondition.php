<?php namespace MelonCart\Shop\PriceRules;

use Db;
use RainLab\Location\Models\Country as CountryModel;
use RainLab\Location\Models\State as StateModel;
use MelonCart\Shop\Classes\RuleConditionBase;
use RainLab\Notify\Classes\ModelAttributesConditionBase;
use ApplicationException;

class CartAttributeCondition extends ModelAttributesConditionBase
{
    protected $modelClass = \MelonCart\Shop\Models\UserCartItem::class;

    public function getConditionType()
    {
        return RuleConditionBase::TYPE_CART_ATTRIBUTE;
    }

    public function getGroupingTitle()
    {
        return 'Shopping cart attribute';
    }

    /**
     * Returns a condition title for displaying in the condition settings form
     */
    public function getTitle()
    {
        return 'Shopping cart attribute';
    }

    public function defineModelAttributes($type = null)
    {
        if ($type === RuleConditionBase::TYPE_CART_PRODUCT_ATTRIBUTE) {
            return 'attributes_product.yaml';
        }

        return 'attributes.yaml';
    }

    public function getValueDropdownOptions()
    {
        $attribute = $this->host->subcondition;

        if ($attribute == 'shipping_state') {
            $records = CountryModel::with(['states' => function($q) {
                $q->orderBy('name');
            }])->orderBy('name')->get();

            return $this->formatCountryStateValues($records);
        }

        return parent::getValueDropdownOptions();
    }

    public function getCustomTextValue()
    {
        $attribute = $this->host->subcondition;

        if ($attribute == 'shipping_state') {
            $ids = explode(',', $this->host->value);

            $records = CountryModel::with(['states' => function($q) use ($ids) {
                $q->whereIn('id', $ids)->orderBy('name');
            }])->orderBy('name')->get();

            $results = $this->formatCountryStateValues($records);

            if (count($results) > 1) {
                return '('.implode(', ', $results).')';
            }
            else {
                return reset($results);
            }
        }

        return false;
    }

    protected function formatCountryStateValues($countries)
    {
        $result = [];

        foreach ($countries as $country) {
            foreach ($country->states as $state) {
                $result[$state->id] = $country->name.'/'.$state->name;
            }
        }

        return $result;
    }

    // @todo
    // protected function get_reference_visible_columns($model, $modelColumns)
    // {
    //     if ($model instanceof StateModel) {
    //         return ['country_state_name'];
    //     }

    //     return parent::get_reference_visible_columns($model, $modelColumns);
    // }

    public function get_reference_search_fields($model, $columns)
    {
        if ($model instanceof StateModel) {
            return ["concat(shop_countries.name, '/', shop_states.name)"];
        }

        return $columns;
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

        if ($attribute == 'shipping_method') {
            if (!array_key_exists('shipping_method', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the shipping_method element is not found in the condition parameters.');
            }

            $shippingMethod = $params['shipping_method'];
            return parent::evalIsTrue(null, $shippingMethod);
        }

        if ($attribute == 'payment_method') {
            if (!array_key_exists('payment_method', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the payment_method element is not found in the condition parameters.');
            }

            $paymentMethod = $params['payment_method'];
            return parent::evalIsTrue(null, $paymentMethod);
        }

        if ($attribute == 'shipping_country') {
            if (!array_key_exists('shipping_address', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the shipping_address element is not found in the condition parameters.');
            }

            $shippingAddress = $params['shipping_address'];

            $testCountry = Shop_Country::create();
            $testCountry->id = $shippingAddress->country;

            return parent::evalIsTrue(null, $testCountry);
        }

        if ($attribute == 'shipping_state') {
            if (!array_key_exists('shipping_address', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the shipping_address element is not found in the condition parameters.');
            }

            $shippingAddress = $params['shipping_address'];

            $testState = Shop_Country::create();
            $testState->id = $shippingAddress->state;

            return parent::evalIsTrue(null, $testState);
        }

        if ($attribute == 'shipping_zip') {
            if (!array_key_exists('shipping_address', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the shipping_address element is not found in the condition parameters.');
            }

            $shippingAddress = $params['shipping_address'];

            return parent::evalIsTrue(null, $shippingAddress->zip);
        }

        if ($attribute == 'subtotal') {
            if (!array_key_exists('subtotal', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the subtotal element is not found in the condition parameters.');
            }

            return parent::evalIsTrue(null, $params['subtotal']);
        }

        if ($attribute == 'total_quantity') {
            if (!array_key_exists('cart_items', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the cart_items element is not found in the condition parameters.');
            }

            $quantity = 0;
            foreach ($params['cart_items'] as $cartItem) {
                $quantity += $cartItem->quantity;
            }

            return parent::evalIsTrue(null, $quantity);
        }

        if ($attribute == 'total_weight') {
            if (!array_key_exists('cart_items', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the cart_items element is not found in the condition parameters.');
            }

            $weight = 0;
            foreach ($params['cart_items'] as $cartItem) {
                $weight += $cartItem->total_weight();
            }

            return parent::evalIsTrue(null, $weight);
        }

        if ($attribute == 'total_discount') {
            if (!array_key_exists('cart_discount', $params)) {
                throw new ApplicationException('Error evaluating the cart attribute condition: the cart_discount element is not found in the condition parameters.');
            }

            return parent::evalIsTrue(null, $params['cart_discount']);
        }

        return false;
    }
}
