<?php namespace MelonCart\Shop\Models;

use Model;

/**
 * ProductAttribute Model
 */
class ProductOption extends Model
{
    use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_product_options';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
