<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * Order Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_orders';

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
    public $hasMany = [
        'items' => ['MelonCart\Shop\Models\OrderItem'],
    ];
    public $belongsTo = [
        'customer' => ['RainLab\User\Models\User'],
        'shipping_method' => ['MelonCart\Shop\Models\ShippingMethod'],
        'status' => ['MelonCart\Shop\Models\OrderStatus'],
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

    /**
     * @param int $new_status
     * @return mixed
     */
    public function updateStatus($new_status)
    {
        $this->status_id = $new_status;
        return $this->save();
    }
}
