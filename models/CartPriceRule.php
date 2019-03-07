<?php namespace MelonCart\Shop\Models;

use Model;
use MelonCart\Shop\Classes\RuleActionBase;

/**
 * CartPriceRule Model
 */
class CartPriceRule extends PriceRuleBase
{
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_cart_rules';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array The rules to be applied to the data.
     */
    public $rules = [
        'name' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'user_groups' => [
            \RainLab\User\Models\UserGroup::class,
            'table' => 'mc_shop_cart_rules_user_groups',
            'key' => 'cart_rule_id'
        ]
    ];

    public $hasMany = [
        'rule_conditions' => [
            PriceRuleCondition::class,
            'key' => 'rule_host_id',
            'conditions' => "rule_host_type='cart' and rule_parent_id is null"
        ],
        'products_conditions' => [
            PriceRuleCondition::class,
            'key' => 'rule_host_id',
            'conditions' => "rule_host_type='cart-products' and rule_parent_id is null"
        ],
    ];

    public $belongsTo = [
        'coupon' => Coupon::class
    ];

    public function getActionClassNameOptions()
    {
        $result = [];

        $actions = RuleActionBase::findActionsByType(RuleActionBase::TYPE_CART);

        foreach ($actions as $actionClass => $actionObj) {
            $result[$actionClass] = $actionObj->getName();
        }

        asort($result);
        return $result;
    }
}
