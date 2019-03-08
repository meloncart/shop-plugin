<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * UserCartItem Model
 */
class UserCartItem extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_user_cart_items';

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
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Returns a list of attributes which can be used in price rule conditions
     */
    // public function getConditionAttributes($type = null)
    // {
    //     if ($type === RuleConditionBase::TYPE_CART_PRODUCT_ATTRIBUTE) {
    //         return [
    //             'price' =>       ['Price in the shopping cart', RuleConditionBase::CAST_FLOAT],
    //             'quantity' =>    ['Quantity in the shopping cart', RuleConditionBase::CAST_INTEGER],
    //             'row_total' =>   ['Row total in the shopping cart', RuleConditionBase::CAST_FLOAT],
    //             'discount' =>    ['Total line item discount', RuleConditionBase::CAST_FLOAT],
    //             'bundle_item' => ['Product is a bundle item', RuleConditionBase::CAST_BOOLEAN],
    //         ];
    //     }

    //     return [
    //         'subtotal'         => ['Subtotal', RuleConditionBase::CAST_FLOAT],
    //         'total_quantity'   => ['Total quantity', RuleConditionBase::CAST_INTEGER],
    //         'total_discount'   => ['Total weight', RuleConditionBase::CAST_FLOAT],
    //         'total_weight'     => ['Total cart discount', RuleConditionBase::CAST_FLOAT],
    //         'shipping_zip'     => ['Shipping ZIP/postal code', asdasddsa::CAST_FLOAT],
    //         'shipping_country' => ['Shipping Country', RuleConditionBase::CAST_RELATION],
    //         'shipping_state'   => ['Shipping State', RuleConditionBase::CAST_RELATION],
    //         'shipping_method'  => ['Shipping Method', RuleConditionBase::CAST_RELATION],
    //         'payment_method'   => ['Payment Method', RuleConditionBase::CAST_RELATION],
    //     ];
    // }
}
