<?php namespace MelonCart\Shop\Models;

use Model;
use Str;

/**
 * ProductOptionMatrixOption Model
 */
class ProductOMOption extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_product_om_options';

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
    public $hasMany = [
        'recordOptions' => ['MelonCart\Shop\Models\ProductOMRecordOption', 'primaryKey' => 'om_option_id', 'localKey' => 'id'],
        'products' => ['MelonCart\Shop\Models\Product'],
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
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
        'values' => 'required',
    ];

    /**
     * Constructor
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->bindEvent('model.beforeSave', function() {
            $this->code = $this->getUniqueCode();
        });
    }

    public function beforeDelete()
    {
        $this->recordOptions()->delete();
    }

    /**
     * Ensures a unique attribute value, if the value is already used a counter suffix is added.
     * @param string $name The database column name.
     * @param value $value The desired column value.
     * @return string A safe value that is unique.
     */
    protected function getUniqueCode()
    {
        $counter = 1;
        $separator = '_';
        $code = Str::slug($this->title, $separator);

        // Remove any existing suffixes
        $_value = $code;

        while ($this->newUniqueCodeQuery()->where('code', '=', $_value)->count() > 0) {
            $counter++;
            $_value = $code . $separator . $counter;
        }

        return $_value;
    }

    protected function newUniqueCodeQuery()
    {
        $query = $this->newQuery()
            ->where('product_id', '=', $this->product_id);

        if ( !$this->id )
            $query = $query->whereRaw('!ISNULL(id)');
        else
            $query = $query->where('id', '!=', $this->id);

        return $query;
    }

    /**
     * Given an array of [option_id => om_id/value permutations], generates all unique
     * option permutations a record may contain.
     *
     * @param  array  $option_groups An array grouped by option_id of possible [option_id,value] permutations
     * Array
     * (
     *     [1] => Array(
     *              Array(1, S)
     *              Array(1, M)
     *            )
     *
     *     [2] => Array(
     *              Array(2, Black)
     *              Array(2, White)
     *            )
     *
     *     [3] => Array(
     *              Array(3, Light)
     *              Array(3, Heavy)
     *            )
     *  )
     *
     * @return array   Unique permutations that may be attached to a record
     * Array
     *  (
     *      Array
     *      (
     *          Array(1, S)      // om_option_id, value
     *          Array(2, Black)  // om_option_id, value
     *          Array(3, Light)  // om_option_id, value
     *      ),
     *      Array
     *      (
     *          Array(1, M)      // om_option_id, value
     *          Array(2, Black)  // om_option_id, value
     *          Array(3, Light)  // om_option_id, value
     *      ),
     *      ...
     *  )
     */
    public static function generate_permutations(array $option_groups)
    {
        $iter = 0;
        $results = [];
        while (1) {
            $num = $iter++;
            $pick = array();

            foreach ($option_groups as $refineGroup => $groupValues) {
                $r = $num % count($groupValues);
                $num = ($num - $r) / count($groupValues);
                $pick[] = $groupValues[$r];
            }

            if ($num > 0) {
                break;
            }

            $results[] = $pick;
        }

        return $results;
    }
}
