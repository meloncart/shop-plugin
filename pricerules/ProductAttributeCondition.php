<?php namespace MelonCart\Shop\PriceRules;

use Db;
use MelonCart\Shop\Classes\RuleConditionBase;
use RainLab\Notify\Classes\ModelAttributesConditionBase;
use ApplicationException;

class ProductAttributeCondition extends ModelAttributesConditionBase
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
        return 'Product or product attribute';
    }

    public function getTitle()
    {
        return 'Product attribute condition';
    }

    public function getValueControlType()
    {
        $hostObj = $this->host;
        $attribute = $hostObj->subcondition;
        $operator = $hostObj->operator;

        if ($attribute == 'product') {
            return 'multi_value';
        }

        if ($attribute == 'on_sale') {
            return 'dropdown';
        }

        return parent::getValueControlType();
    }

    public function getCustomTextValue()
    {
        $attribute = $this->host->subcondition;

        if ($attribute == 'on_sale') {
            return $this->host->value == 'true' ? 'TRUE' : 'FALSE';
        }

        return false;
    }

    public function getOperatorOptions()
    {
        $options = [];
        $hostObj = $this->host;
        $attribute = $hostObj->subcondition;

        $model = $this->getModelObj();
        $definitions = $model->get_column_definitions();

        if (!isset($definitions[$attribute]) || $attribute == 'on_sale') {
            if ($attribute != 'product' && $attribute != 'on_sale') {
                $options = ['none' => 'Unknown attribute selected'];
            }
            elseif ($attribute == 'product') {
                $options = [
                    'one_of' => 'is one of',
                    'not_one_of' => 'is not one of'
                ];
            }
            elseif ($attribute == 'on_sale') {
                $options = [
                    'is' => 'is'
                ];
            }

            return $options;
        }
        else {
            return parent::getOperatorOptions();
        }
    }

    public function getConditionType()
    {
        return RuleConditionBase::TYPE_PRODUCT;
    }

    public function getValueDropdownOptions()
    {
        $hostObj = $this->host;
        $attribute = $hostObj->subcondition;

        if ($attribute == 'product') {
            $products = Db::table('shop_products')
                ->applyEnabled() // disable_completely is null or disable_completely = 0
                ->orderBy('name')
                ->lists('name', 'id');

            $result = [];
            foreach ($products as $product) {
                $result[$product->id] = h($product->name);
            }

            return $result;
        }
        elseif ($attribute == 'on_sale') {
            return [
                'false' => 'FALSE (there are NO Catalog Price Rules defined for the product)',
                'true'=>'TRUE (there are Catalog Price Rules defined for the product)'
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

        if ($attribute == 'product') {
            $this->referenceInfo = [];
            $this->referenceInfo['reference_model'] = new Shop_Product();
            $this->referenceInfo['columns'] = array('name');

            return $this->referenceInfo = (object) $this->referenceInfo;
        }
        elseif ($attribute == 'on_sale') {
            return null;
        }

        return parent::prepareReferenceListInfo();
    }

    public function prepareFilterQuery($query, $model)
    {
        if (get_class($model) == 'Shop_Product') {
            $query->where('grouped is null and (disable_completely is null or disable_completely=0)');
        }
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

        if (!array_key_exists('product', $params)) {
            throw new ApplicationException('Error evaluating the product attribute condition: the product element is not found in the condition parameters.');
        }

        $attribute = $hostObj->subcondition;

        if (
            $attribute != 'current_price' &&
            $attribute != 'product' &&
            $attribute != 'categories' &&
            $attribute != 'on_sale' &&
            $attribute != 'manufacturer_link'
        ) {
            /*
             * If om_record (Option Matrix record) key exists in the parameters, try to load the attribute value from it,
             * instead of loading it from the product.
             */
            $attributeValue = '__no_value_provided__';
            if (
                isset($params['om_record']) &&
                $params['om_record'] instanceof Shop_OptionMatrixRecord &&
                $params['om_record']->is_property_supported($attribute) === true
            ) {
                /*
                 * Condition attribute 'price' actually refers to base price.
                 */
                if ($attribute == 'price') {
                    $attribute = 'base_price';
                }

                $attributeValue = $params['product']->om($attribute, $params['om_record']);
            }

            return parent::evalIsTrue($params['product'], $attributeValue);
        }

        if ($attribute == 'on_sale') {
            $omRecord = (isset($params['om_record']) && $params['om_record'] instanceof Shop_OptionMatrixRecord) ? $params['om_record'] : null;
            $testObject = $omRecord ? $omRecord : $params['product'];

            if ($testObject->on_sale && !Shop_Product::is_sale_price_or_discount_invalid($testObject->sale_price_or_discount) && strlen($testObject->sale_price_or_discount)) {
                if ($hostObj->value == 'false') {
                    return false;
                }
                else {
                    return true;
                }
            }

            $hasPriceRules = false;
            if (strlen($testObject->price_rules_compiled)) {
                try {
                    $price_rules = unserialize($testObject->price_rules_compiled);
                    if ($price_rules) {
                        $hasPriceRules = true;
                    }
                } catch (Exception $ex) {}
            }

            if ($hostObj->value == 'false') {
                return !$hasPriceRules;
            }
            else {
                return $hasPriceRules;
            }
        }

        if ($attribute == 'manufacturer_link') {
            return parent::evalIsTrue($params['product'], $params['product']->manufacturer);
        }

        if ($attribute == 'current_price') {
            if (!array_key_exists('current_price', $params)) {
                throw new ApplicationException('Error evaluating the product attribute condition: the current_price element is not found in the condition parameters.');
            }

            $currentPrice = $params['current_price'];
            return parent::evalIsTrue($params['product'], $currentPrice);
        }

        if ($attribute == 'categories') {
            return parent::evalIsTrue($params['product'], $params['product']->list_category_ids());
        }

        if ($attribute == 'product') {
            $test_product = new Shop_Product(null, array('no_column_init'=>true, 'no_validation'=>true));
            $test_product->id = $params['product']->grouped ? $params['product']->product_id : $params['product']->id;

            return parent::evalIsTrue($params['product'], $test_product);
        }

        return false;
    }
}
