<?php namespace MelonCart\Shop\Classes;

use ApplicationException;
use JsonSerializable;
use MelonCart\Shop\Models\Product;
use MelonCart\Shop\Models\ProductOption;

class CartItem implements JsonSerializable
{
    /**
     * @var Product
     */
    public $product;

    /**
     * @var int
     */
    public $qty = 1;

    /**
     * @var double
     */
    public $price = 0;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $extras = [];

    /**
     * CartItem constructor.
     * @param Product $product
     * @param $quantity
     * @param array $details   ['price' => 0.10, 'options' => [], 'extras' => []]
     */
    public function __construct(Product $product, $quantity, array $details)
    {
        $this->product = $product;
        $this->qty = $quantity;
        $this->price = $details['price'];
        $this->options = (array)@$details['options'];
        $this->extras = (array)@$details['extras'];
    }

    public function getOptionsStr($delimiter = "\n")
    {
        $option_titles = ProductOption::whereIn('id', array_keys($this->options))
            ->where('product_id', '=', $this->product->id)
            ->lists('title', 'id');

        // Clear out any options that no longer exist for this product
        $this->options = array_only($this->options, array_keys($option_titles));

        $result = array();

        foreach ( $this->options as $id => $value )
        {
            $result[] = $option_titles[$id].': '.$value;
        }

        return implode($delimiter, $result);
    }

    /**
     * @return double
     */
    public function getSubtotal()
    {
        return $this->price * $this->qty;
    }

    /**
     * @return double
     */
    public function getHeight()
    {
        return $this->product->height * $this->qty;
    }

    /**
     * @return double
     */
    public function getWidth()
    {
        return $this->product->width * $this->qty;
    }

    public function setPrice($price)
    {
        $this->price = doubleval($price);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->product->id,
            'qty' => $this->qty,
            'price' => $this->price,
            'options' => $this->options,
            'extras' => $this->extras,
        ];
    }

    /**
     * Determines if a given product/selection to be added matches this cart item.
     *
     * @param Product $product
     * @param array $options
     * @return bool
     */
    public function matches(Product $product, array $options)
    {
        return
            $product->id == $this->product->id &&
            $options == $this->options;
    }
}