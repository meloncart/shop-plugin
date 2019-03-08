<?php namespace MelonCart\Shop\Models;

use Model;
use DB;
use MelonCart\Shop\Models\ProductOMOption;
use MelonCart\Shop\Models\ProductOMRecordOption;

/**
 * ProductOptionMatrixRecord Model
 */
class ProductOMRecord extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_product_om_records';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['option_fields'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'recordOptions' => ['MelonCart\Shop\Models\ProductOMRecordOption', 'primaryKey' => 'om_record_id', 'localKey' => 'id'],
        'optionNames' => ['MelonCart\Shop\Models\ProductOMOption', 'primaryKey' => 'product_id', 'localKey' => 'product_id'],
    ];
    // Commented out due to bugged implementation of hasManyThrough - https://github.com/octobercms/october/issues/556
    // public $hasManyThrough = [
    //     'optionNames' => ['MelonCart\Shop\Models\ProductOMOption', 'through' => 'MelonCart\Shop\Models\Product', 'primaryKey' => 'id', 'throughKey' => 'product_id']
    // ];
    public $belongsTo = [
        'product' => ['MelonCart\Shop\Models\Product'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => ['System\Models\File', 'order' => 'sort_order'],
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'cost'                  => 'numeric',
        'price'                 => 'required_if:product_id,0:update,numeric',
        'sku'                   => 'required_if:product_id,0:update',
        'weight'                => 'numeric',
        'width'                 => 'numeric',
        'height'                => 'numeric',
        'depth'                 => 'numeric',
        'units_in_stock'        => 'integer',
    ];

    public $cache = [];

    /**
     * Constructor
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // if ( $this->exists() && $this->product )
        // {
        //     $this->cache['customColumnNames'] = $this->customColumnNames()->lists('code', 'id')
        // }

        // $this->bindEvent('model.getAttribute', function($key, $attr) {
        //     if ( empty($this->attributes['id']) || $attr || empty($this->attributes['product_id']) )
        //         return $attr;


        //     $code_ids = ProductOMOption::where('product_id', '=', $this->attributes['product_id'])->lists('id', 'code');
        //     if ( isset($code_ids[$key]) )
        //     {
        //         $recordOption = ProductOMRecordOption::where(['om_record_id' => $this->attributes['id'], 'om_option_id' => $code_ids[$key]])->first();
        //         if ( $recordOption )
        //             return $recordOption->value;
        //     }
        // });
    }

    public function beforeDelete()
    {
        $this->recordOptions()->delete();
    }

    /**
     * Filters records to only those with a given list of om options
     *
     * Example usage:
     * print_r(MelonCart\Shop\Models\ProductOMRecord::withOptionValues([
     *     [1, 'S'],
     *     [2, 'Black'],
     *     [3, 'Light'],
     * ])->first()->toArray());
     *
     * @param  October\Rain\Database\Builder $query
     * @param  array                         $options A list of [om_option_id, value] fields for each om record option
     *      Array
     *      (
     *          Array(1, M)      // om_option_id, value
     *          Array(2, Black)  // om_option_id, value
     *          Array(3, Light)  // om_option_id, value
     *      ),
     *
     * @return October\Rain\Database\Builder
     */
    public function scopeWithOptionValues($query, array $options)
    {
        // Get om_recordoptions table name
        $ro_table = (new ProductOMRecordOption)->getTable();

        foreach ( $options as $key => $option )
        {
            $option = array_values($option); // Make sure the array looks like [om_option_id, value] without keys
            $option_alias = 'omo'.$key; // We're joining the same table multiple times, so create a unique alias for this join

            // Join the option table with given values.
            // Equates to JOIN $tbl tX ON tX.om_record_id=this.id AND tX.om_option_id=foo AND tX.value=bar
            $query
                ->join("$ro_table AS $option_alias", "$option_alias.om_record_id", '=', $this->getQualifiedKeyName())
                ->where("$option_alias.om_option_id", "=", $option[0])
                ->where("$option_alias.value", "=", $option[1]);
        }

        return $query;
    }

    /**
     * Includes the given option codes in the results set. If they don't exist,
     * an empty string is returned.
     *
     * Example usage:
     * print_r(MelonCart\Shop\Models\ProductOMRecord::withOptions(
     *     [1 => 'color', 2 => 'size', 3 => 'weight'],
     * )->first()->toArray());
     *
     * @param  October\Rain\Database\Builder $query
     * @param  array                         $options A list of [om_option_id => field_alias]
     *
     * @return October\Rain\Database\Builder
     */
    public function scopeIncludeOptions($query, array $options)
    {
        // Get om_recordoptions, om_options table names
        $ro_table = (new ProductOMRecordOption)->getTable();

        foreach ( $options as $om_option_id => $code )
        {
            $ro_alias = 'omro'.$om_option_id; // We're left joining the same table multiple times, so create a unique alias for this join

            // Join the option table with given values.
            // Equates to LEFT JOIN om_recordoptions tX ON tX.om_record_id=this.id AND tX.om_option_id=y
            // For more information see http://stackoverflow.com/a/17736960
            $query
                ->leftJoin("$ro_table AS $ro_alias", function($join) use ($ro_alias, $om_option_id) {
                    $join->on("$ro_alias.om_record_id", '=', $this->getQualifiedKeyName());
                    $join->on("$ro_alias.om_option_id", "=", DB::raw($om_option_id));
                });
        }

        return $query;
    }

    // public function getOptions()
    // {
    //     $results = $this->options()


    //     $o_table = (new \MelonCart\Shop\Models\ProductOMOption)->getTable();
    //     $results = \MelonCart\Shop\Models\ProductOMRecordOption::select("$o_table.code, value")
    //         ->join($o_table, "$o_table.id", '=', $this->getTableName().'.om_record_id')
    //         ->where('om_record_id')
    //     print_r(whereHas('omOption', function($query) {$query->searchWhere('om_record_id', )})->where('om_record_id', '=', 1042)->get()->toArray());
    // }
}
