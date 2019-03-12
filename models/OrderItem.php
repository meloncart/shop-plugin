<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * Order Model
 */
class OrderItem extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_order_items';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'taxes' => ['MelonCart\Shop\Models\OrderItemTax'],
    ];
    public $belongsTo = [
        'order' => ['MelonCart\Shop\Models\Order'],
        'product' => ['MelonCart\Shop\Models\Product'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * @var array Validation rules
     */
    public $rules = [];

}
