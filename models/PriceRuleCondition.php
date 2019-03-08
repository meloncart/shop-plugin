<?php namespace MelonCart\Shop\Models;

use Model;
use MelonCart\Shop\Classes\RuleCompoundCondition;
use RainLab\Notify\Models\RuleCondition as RuleConditionModel;
use SystemException;

/**
 * PriceRuleCondition Model
 */
class PriceRuleCondition extends RuleConditionModel
{
    const TYPE_CART = 'cart';
    const TYPE_CATALOG = 'catalog';
    const TYPE_CART_PRODUCTS = 'cart-products';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_price_rule_conditions';

    /**
     * @var array Relations
     */
    public $hasMany = [
        'children' => [self::class, 'key' => 'rule_parent_id'],
    ];

    public $belongsTo = [
        'parent' => [self::class, 'key' => 'rule_parent_id'],
    ];

    public function getRootConditionClass()
    {
        return RuleCompoundCondition::class;
    }
}
