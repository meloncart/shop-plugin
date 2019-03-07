<?php namespace MelonCart\Shop\Components;

use October\Rain\Scaffold\Templates\Component;
use URL;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use MelonCart\Shop\Models\Product;
use MelonCart\Shop\Classes\Cart as ShoppingCart;
use MelonCart\Shop\Classes\ComponentPageHelper;
use October\Rain\Exception\AjaxException;

class Cart extends ComponentBase
{
    public $checkoutPage;
    public $cartPage;
    public $products;
    public $cart;

    public function componentDetails()
    {
        return [
            'name'        => 'Cart',
            'description' => 'Allows for manipulating and displaying of the cart.'
        ];
    }

    public function defineProperties()
    {
        return [
            //'isPrimary' => [
            //    'title'             => 'Primary?',
            //    'type'              => 'checkbox',
            //    'description'       => 'Is this the primary page your cart is located on?',
            //],
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->setPageProp('cart', ShoppingCart::instance());

        $this->setPageProp('cartPage', ComponentPageHelper::instance()->cart());
        $this->setPageProp('checkoutPage', ComponentPageHelper::instance()->checkout());
    }

    public function onAddProduct()
    {
        $id = intval(post('id'));
        $qty = intval(post('qty'));

        $product = Product::applyFrontend()->find($id);
        if ( !$product )
            throw new AjaxException("Invalid product.");

        try {
            ShoppingCart::instance()->addItem($product, $qty, $_POST);
        } catch (ApplicationException $e) {
            throw new AjaxException($e->getMessage());
        }
    }

    public function onSetQuantity()
    {
        $key = post('key');
        $qty = intval(post('qty'));
        $cart = ShoppingCart::instance();

        if ( $qty < 0 )
            throw new AjaxException("Invalid quantity.");

        if ( !$cart->hasItem($key) )
            throw new AjaxException("Cart item not found.");

        try {
            $cart->setQuantity($key, $qty);
        } catch (ApplicationException $e) {
            throw new AjaxException($e->getMessage());
        }
    }

    protected function setPageProp($property, $value)
    {
        $this->page[$property] = $this->{$property} = $value;
    }
}
