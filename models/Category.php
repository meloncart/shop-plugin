<?php namespace MelonCart\Shop\Models;

use URL;
use Model;
use Cms\Classes\Page;
use MelonCart\Shop\Classes\ComponentPageHelper;

/**
 * Category Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;
    use \October\Rain\Database\Traits\Sluggable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_categories';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['title', 'slug', 'short_desc', 'description'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'products' => ['MelonCart\Shop\Models\Product', 'table' => 'meloncart_shop_products_categories', 'timestamps' => true],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public $slugs = ['slug' => 'title'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i'],
        'description' => 'required',
    ];

    protected $permalink;

    /**
     * Return a category with matching slug
     *
     * @param string $slug   Nested or unnested slug. eg foo/bar/baz
     * @return Category|null
     */
    public function scopeFindByNestedSlug($query, $slug)
    {
        $parts = explode('/', $slug);
        // Single part slug - just grab the category from our slug
        if ( count($parts) == 1 )
            return self::where('slug', '=', $parts[0])->first();
        // Multi part slug. Grab the category from the last part of our slug,
        // get its ancestors, ensure everything matches up with our nested slug.
        else
        {
            $category = self::where('slug', '=', end($parts))->first();
            if ( !$category ) return null;

            $lineage = $category->getParentsAndSelf();
            // We either have more parts in our slug than there are categories in
            // this ancestry or vice versa.
            if ( $lineage->count() != count($parts) )
                return null;

            // Loop through lineage looking for slug mismatches
            foreach ( $lineage as $cat )
                if ( array_shift($parts) != $cat->slug )
                    return null;

            // No mismatches - we've found our category!
            return $category;
        }
    }

    public function getNestedSlug()
    {
        $lineage = $this->getParentsAndSelf();
        $slug = [];

        foreach ( $lineage as $cat )
            $slug[] = $cat->slug;

        return implode('/', $slug);
    }

    public function permalink(array $params = [])
    {
        if ( !$this->permalink )
        {
            $params['slug'] = $this->slug;
            $this->permalink = ComponentPageHelper::instance()
                ->category($params, '/category/'.$params['slug']);
        }

        return $this->permalink;
    }
}
