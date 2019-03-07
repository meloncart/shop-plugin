<?php namespace MelonCart\Shop\Models;

use Model;
use MelonCart\Shop\Classes\RuleActionBase;

/**
 * CatalogPriceRule Model
 */
class CatalogPriceRule extends PriceRuleBase
{
    use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_catalog_rules';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'user_groups' => [
            \RainLab\User\Models\UserGroup::class,
            'table' => 'mc_shop_cart_rules_user_groups',
            'key' => 'catalog_rule_id'
        ]
    ];

    public $hasMany = [
        'rule_conditions' => [
            PriceRuleCondition::class,
            'key' => 'rule_host_id',
            'conditions' => "rule_host_type='catalog' and rule_parent_id is null"
        ],
    ];

    public function getActionClassNameOptions()
    {
        $result = [];

        $actions = RuleActionBase::findActionsByType(RuleActionBase::TYPE_PRODUCT);

        foreach ($actions as $actionClass => $actionObj) {
            $result[$actionClass] = $actionObj->getName();
        }

        asort($result);
        return $result;
    }
}
