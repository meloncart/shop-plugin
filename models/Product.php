<?php namespace MelonCart\Shop\Models;

use URL;
use Model;
use Cms\Classes\Page;
use MelonCart\Shop\Classes\ComponentPageHelper;

/*
@TODO Implement a global scope

Sort of
https://github.com/octobercms/library/blob/master/src/Database/NestedTreeScope.php
7:35 static::addGlobalScope(new NestedTreeScope);
7:35 If you put this on your model, it will always filter it by the apply() method, and the remove() it... kinda like a DB migration
7:36 https://github.com/octobercms/library/blob/master/src/Database/Traits/NestedTree.php#L82
7:36 Yup, or in a trait boot method
 */

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_products';

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
        //'optionNames' => ['MelonCart\Shop\Models\ProductOMOption', 'foreignKey' => 'product_id'],
        //'optionsList' => ['MelonCart\Shop\Models\ProductOMRecord'],
        'product_options' => ['MelonCart\Shop\Models\ProductOption'],
        'product_extras' => ['MelonCart\Shop\Models\ProductExtra'],
    ];

    public $belongsTo = [
        'manufacturer' => ['MelonCart\Shop\Models\Manufacturer'],
        'tax_class' => ['MelonCart\Shop\Models\TaxClass'],
        'product_type' => ['MelonCart\Shop\Models\ProductType', 'foreignKey' => 'product_type_id'],
        //'baseOptions' => ['MelonCart\Shop\Models\ProductOMRecord', 'foreignKey' => 'default_om_id']
    ];
    public $belongsToMany = [
        'categories' => ['MelonCart\Shop\Models\Category', 'table' => 'meloncart_shop_products_categories', 'timestamps' => true],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => ['System\Models\File', 'order' => 'sort_order'],
    ];

    public $slugs = ['slug' => 'title'];

    protected $permalink;

    /**
     * @var array Validation rules
     */
    public $rules = [
        // On all pages
        'title'                 => 'required',
        'slug'                  => 'required',
        'description'           => 'required:update',
        'manufacturer_id'       => 'required:update|exists:meloncart_shop_manufacturers,id',
        'tax_class_id'          => 'required:update|exists:meloncart_shop_tax_classes,id',
        'product_type_id'       => 'required:update|exists:meloncart_shop_product_types,id',
        'sale_price'            => 'required_if:is_on_sale,1|regex:/^-?\d+(\.\d\d)?\%?$/',
    ];

    public static $allowedSortingOptions = ['title'];

    //public static function boot()
    //{
    //    parent::boot();
    //
    //    // This global scope loads the baseOptions record when a product or
    //    // group of products is loaded
    //    static::addGlobalScope(new ProductRecordScope);
    //}

    //public function beforeCreate()
    //{
    //    // @TODO This occurs after fill(). We need something before that...
    //    // Newly created products won't have this yet. So add it.
    //    $this->baseOptions = new ProductOMRecord;
    //
    //    // Save the default options. This must be done beforeCreate so that
    //    // the $product->default_om_id field can be set
    //    $this->baseOptions->save();
    //}
    //
    //public function beforeDelete()
    //{
    //    if ( is_object($this->baseOptions) )
    //    {
    //        foreach ( $this->optionNames as $option )
    //            $option->delete();
    //        $this->optionsList()->delete();
    //        $this->baseOptions->delete();
    //    }
    //}

    public function scopeApplyFilters($query, array $options = [])
    {
        $options = array_merge([
            'id' => [],
            'id__not_in' => [],
            'slug' => [],
            'slug__not_in' => [],
            'author_id' => [],
            'author_id__not_in' => [],
            'category_id' => [],
            'category_id__not_in' => [],
            'tag_id' => [],
            'tag_id__not_in' => [],
            'visibility' => ['public'],
            'status' => ['published'],
            'date' => [
                'year' => 0,
                'month' => 0,
                'day' => 0,
                'hour' => 0,
                'minute' => 0,
                'second' => 0,
                'after' => '',
                'before' => '',
            ],
        ], $options);

        // Sanitize
        $arrayOptions = ['id', 'id__not_in', 'slug', 'slug__not_in', 'author_id', 'author_id__not_in', 'category_id', 'category_id__not_in'];
        foreach ( $arrayOptions as $option )
            if ( !is_array($options[$option]) ) $options[$option] = [$options[$option]];

        // id
        if ( $options['id'] ) $query->whereIn('id', $options['id']);
        if ( $options['id__not_in'] ) $query->whereNotIn('id__not_in', $options['id__not_in']);

        // slug
        if ( $options['slug'] ) $query->whereIn('slug', $options['slug']);
        if ( $options['slug__not_in'] ) $query->whereIn('slug__not_in', $options['slug__not_in']);

        // author_id
        if ( $options['author_id'] ) $query->whereIn('author_id', $options['author_id']);
        if ( $options['author_id__not_in'] ) $query->whereNotIn('author_id__not_in', $options['author_id__not_in']);

        return $query;
    }

    public function scopeApplyFrontend($query, array $options = [])
    {
        /*
         * Default options
         */
        $options = array_merge([
            'status'     => 'published',
            'visibility' => 'public',
        ], $options);

        return $this->scopeApplyFilters($query, [

        ]);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', '=', true);
    }

    /**
     * Lists posts for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontend($query, array $options = [])
    {
        /*
         * Default options
         */
        $options = array_merge([
            'page'       => 1,
            'perPage'    => 30,
            'sort'       => 'title',
            'categories' => null,
            'search'     => '',
            'is_enabled' => true,
        ], $options);

        $searchableFields = ['title'];

        $query = $this->scopeApplyFrontend($query, [
            'is_enabled' => $options['is_enabled'],
        ]);

        /*
         * Sorting
         */
        foreach ((array)$options['sort'] as $sorting) {

            $parts = explode(' ', $sorting);
            if (count($parts) < 2) array_push($parts, 'desc');
            list($sortField, $sortDirection) = $parts;

            if ( !in_array($sortField, self::$allowedSortingOptions) )
                $query->orderBy($sortField, $sortDirection);
            else if ( $sortField == 'random' )
                $query->orderBy(DB::raw('RAND()'));
        }

        /*
         * Search
         */
        $search = trim($options['search']);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        if ( !empty($options['categories'])) {
            $categories = (array)$options['categories'];
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        return $query->paginate($options['perPage'], $options['page']);
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
                ->product($params, '/product/'.$params['slug']);
        }

        return $this->permalink;
    }

    /**
     * Returns the product price taking into account sale prices
     *
     * @return double
     */
    public function getPriceAttribute()
    {
        $price = $this->base_price;

        // Is the product on sale?
        if ( $this->is_on_sale && strlen($this->sale_price) )
        {
            // Percentages off
            if ( substr($this->sale_price, -1) == '%' )
            {
                $price -= $price * doubleval(substr($this->sale_price, 0, strlen($this->sale_price)-1)) / 100;
            }
            // Amount off
            elseif ( substr($this->sale_price, 0, 1) == '-' )
                $price += $this->sale_price;
            // Exact price
            else
                $price = $this->sale_price;
        }

        return $price;
    }

    // OM data on Product page load
    //public function getOptionsListDataSourceValues()
    //{
    //    return $this->optionsList->toArray();
    //}

    /**
     * Returns a list of attributes which can be used in price rule conditions
     */
    // public function getConditionAttributes($type = null)
    // {
    //     return [
    //         'name'              => 'Name',
    //         'description'       => 'Long Description',
    //         'short_description' => 'Short Description',
    //         'price'             => ['Base Price', RuleConditionBase::CAST_FLOAT],
    //         'tax_class'         => 'Tax Class',
    //         'sku'               => 'SKU',
    //         'weight'            => ['Weight', RuleConditionBase::CAST_FLOAT],
    //         'width'             => ['Width', RuleConditionBase::CAST_FLOAT],
    //         'height'            => ['Height', RuleConditionBase::CAST_FLOAT],
    //         'depth'             => ['Depth', RuleConditionBase::CAST_FLOAT],
    //         'categories'        => 'Categories',
    //         'current_price'     => ['Price', RuleConditionBase::CAST_FLOAT],
    //         'manufacturer_link' => 'Manufacturer',
    //         'product_type'      => 'Product Type'
    //     ];
    // }
}
