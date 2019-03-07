<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * ProductOMRecordOption Model
 */
class ProductOMRecordOption extends Model
{
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_product_om_record_options';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [
        'omRecord' => ['MelonCart\Shop\Models\ProductOMRecord', 'foreignKey' => 'om_record_id'],
        'omOption' => ['MelonCart\Shop\Models\ProductOMOption', 'foreignKey' => 'om_option_id'],
    ];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
