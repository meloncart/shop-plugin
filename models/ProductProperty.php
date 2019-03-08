<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * ProductProperty Model
 */
class ProductProperty extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_product_properties';

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
}
