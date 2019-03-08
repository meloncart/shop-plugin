<?php namespace MelonCart\Shop\Models;

use Model;
use Backend\Models\UserRole;
use MelonCart\Shop\Models\OrderStatus;

/**
 * OrderStatusTransition Model
 */
class OrderStatusTransition extends Model
{

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_order_status_transitions';

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
    public $hasMany = [];
    public $belongsTo = [
        'from_status' => ['MelonCart\Shop\Models\OrderStatus'],
        'to_status' => ['MelonCart\Shop\Models\OrderStatus'],
        'role' => ['Backend\Models\UserRole'],
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

    public function getToStatusIdOptions($value, OrderStatusTransition $record)
    {
        if ( $record->from_status_id )
            $query = OrderStatus::where('id', '!=', $record->from_status_id)->pluck('title', 'id');
        else
            $query = OrderStatus::pluck('title', 'id');

        return $query;
    }

    public function getRoleIdOptions($value, OrderStatusTransition $record)
    {
        return UserRole::orderBy('name')->pluck('name', 'id');
    }
}
