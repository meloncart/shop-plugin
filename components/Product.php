<?php namespace MelonCart\Shop\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use MelonCart\Shop\Models\Product as ProductModel;
use MelonCart\Shop\Classes\Cart as ShoppingCart;
use October\Rain\Exception\AjaxException;

class Product extends ComponentBase
{
    /**
     * If the post list should be filtered by a category, the model to use.
     * @var \MelonCart\Shop\Models\Product
     */
    public $product;

    public function componentDetails()
    {
        return [
            'name'        => 'Product',
            'description' => 'Displays a Product.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'             => 'Slug',
                'type'              => 'string',
                'default'           => '{{ :slug }}',
            ],
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->product = $this->page['product'] = $this->loadProduct($this->property('slug'));
    }

    /**
     * Return the Category with a given nested Slug
     *
     * @param $slug
     * @return BlogCategory|null
     */
    protected function loadProduct($slug)
    {
        return ProductModel::whereSlug($slug)->first();
    }

    public function onAddToCart()
    {
        $id = intval(post('id'));
        $qty = intval(post('qty'));

        $product = ProductModel::find($id);
        if ( !$product )
            throw new AjaxException("Invalid product.");

        try {
            ShoppingCart::instance()->addItem($product, $qty, $_POST);
        } catch (ApplicationException $e) {
            throw new AjaxException($e->getMessage());
        }
    }
}
