<?php namespace MelonCart\Shop\Models;

use URL;
use Model;
use Cms\Classes\Page;
use MelonCart\Shop\Classes\ComponentPageHelper;

/**
 * Manufacturer Model
 */
class Manufacturer extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;

    public $implement = ['RainLab.Location.Behaviors.LocationModel'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mc_shop_manufacturers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [''];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'products' => ['MelonCart\Shop\Models\Product'],
    ];
    public $belongsTo = [
        'country' => ['RainLab\Location\Models\Country', 'foreignKey' => 'country_id'],
        'state' => ['RainLab\Location\Models\State', 'foreignKey' => 'state_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => ['System\Models\File', 'order' => 'sort_order'],
    ];

    public $slugs = ['slug' => 'title'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'email' => 'email',
        'url' => 'url',
    ];

    protected $permalink;


    public function scopeEnabled($query)
    {
        return $query->where('enabled', '=', true);
    }

    /**
     * Returns this products canonical URL.
     *
     * @param array $params
     * @return string
     */
    public function permalink(array $params = [])
    {
        if ( !$this->permalink )
        {
            $params['slug'] = $this->slug;
            $this->permalink = ComponentPageHelper::instance()
                ->manufacturer($params, '/manufacturer/'.$params['slug']);
        }

        return $this->permalink;
    }
}
