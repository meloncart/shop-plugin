<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * ShippingMethod Model
 */
class ShippingMethod extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_shipping_methods';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['config_data'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'countries' => ['RainLab\Location\Models\Country', 'table' => 'meloncart_shop_shipping_method_countries', 'timestamps' => false]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'handling_fee' => 'numeric',
        'min_weight' => 'numeric',
        'max_weight' => 'numeric',
    ];

    /**
     * Creates a new instance of a form model, used by create actions. This logic
     * can be changed by overriding it in the controller.
     * @return Model
     */
    public function formCreateModelObject()
    {
        $model = $this->createModel();

        return $model;
    }

}
