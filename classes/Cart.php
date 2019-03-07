<?php namespace MelonCart\Shop\Classes;

use Session;
use ApplicationException;
use MelonCart\Shop\Models\Product;

class Cart
{
    use \October\Rain\Support\Traits\Singleton;

    const SESSION_NAME = 'meloncart_cart';

    /**
     * @var array   Array of CartItem
     */
    protected $items = [];

    /**
     * Initialize the singleton free from constructor parameters.
     */
    protected function init()
    {
        $this->load();
    }

    protected function load()
    {
        $items = Session::get(self::SESSION_NAME, '[]');
        if ( is_string($items) && $items )
        {
            $items = json_decode($items, true);
            $this->loadItems($items);
        }
    }

    protected function loadItems(array $items)
    {
        // Get all product instances with a single DB call
        $ids = array_pluck($items, 'id');
        $products = Product::applyFrontend()->whereIn('id', $ids)->get();

        // Load CartItem's into $this->items
        foreach ( $items as $key => $item )
        {
            foreach ( $products as $product )
            {
                if ( $product->id == $item['id'] )
                {
                    try {
                        $this->createCartItem($key, $product, $item['qty'], $item);
                    } catch (ApplicationException $e) {}
                    break;
                }
            }
        }
    }

    public static function reset()
    {
        Session::put(self::SESSION_NAME, json_encode([]));
    }

    protected function save()
    {
        $data = json_encode($this->getItems());
        Session::put(self::SESSION_NAME, $data);
    }

    /**
     * Adds an item to the cart. The products current price will automatically
     * be set.
     *
     * @param Product $product
     * @param $quantity
     * @param array $details
     * @return CartItem
     * @throws ApplicationException
     */
    public function addItem(Product $product, $quantity, array $details = [])
    {
        $quantity = intval($quantity);
        if ( $quantity <= 0 )
            throw new ApplicationException("Invalid quantity.");

        // Sanitize input
        $details = array_only($details, ['options', 'extras']);

        // Ensure valid options provided
        if ( !empty($details['options']) )
        {
            $valid_ids = $product->product_options()->lists('id');
            $details['options'] = array_only($details['options'], $valid_ids);
        }

        // Ensure valid extras provided
        if ( !empty($details['extras']) )
        {
            $valid_ids = $product->product_extras()->lists('id');
            $details['extras'] = array_only($details['extras'], $valid_ids);
        }

        // This will throw an ApplicationException if we don't have enough in stock
        $this->checkProductAvailability($product, $quantity);

        // Check if we have any cart items with the same same options
        $existing = $this->findMatchingItems($product, $details);

        // We have cart items with the same options - just add on to the existing qty value
        if ( $existing )
        {
            $keys = array_keys($existing);
            $key = reset($keys);
            $this->setQuantity($key, $this->getItem($key)->qty + $quantity);
        }
        // Adding new product to cart
        else
        {
            $details['price'] = $product->price;

            $key = $this->generateItemKey();
            $this->createCartItem($key, $product, $quantity, $details);
        }

        $this->save();

        return $this->getItem($key);
    }

    /**
     * Remove a product with given key from the cart.
     *
     * @param $key
     */
    public function removeItem($key)
    {
        if ( $this->hasItem($key) )
            unset($this->items[$key]);

        $this->save();
    }

    /**
     * Sets a new quantity for a cart item. Will revert
     *
     * @param $key
     * @param $quantity
     * @throws ApplicationException
     */
    public function setQuantity($key, $quantity)
    {
        $quantity = intval($quantity);

        // Setting a quantity of 0 removes the product
        if ( $this->hasItem($key) && $quantity == 0 )
        {
            $this->removeProduct($key);
            $this->save();
            return;
        }

        $old_qty = $this->getItem($key)->qty;
        $this->items[$key]->qty = $quantity;
        $product = $this->getItem($key)->product;

        try {
            $this->checkProductAvailability($product, 0);
        } catch (ApplicationException $e) {
            $this->items[$key]->qty = min($this->items[$key]->qty, $old_qty);
            throw $e;
        }

        $this->save();
    }

    /**
     * Returns all current cart items
     *
     * @return array of CartItem
     */
    public function getItems()
    {
        return $this->items;
    }

    public function hasItem($key)
    {
        return isset($this->items[$key]);
    }

    public function getItem($key)
    {
        return $this->hasItem($key) ? $this->items[$key] : null;
    }

    /**
     * Returns total price before tax and shipping.
     *
     * @return double
     */
    public function getSubtotal()
    {
        $total = 0;
        foreach ( $this->getItems() as $item )
            $total += $item->getSubtotal();

        return $total;
    }

    /**
     * @return double
     */
    public function getTotalHeight()
    {
        $total = 0;
        foreach ( $this->getItems() as $item )
            $total += $item->getTotalHeight();

        return $total;
    }

    /**
     * @return double
     */
    public function getTotalWidth()
    {
        $total = 0;
        foreach ( $this->getItems() as $item )
            $total += $item->getTotalWidth();

        return $total;
    }

    public function getTax()
    {
        return 0;
    }

    /**
     * Checks if a product is in stock taking into considering the number trying
     * to be added and any existing cart items. Throws an ApplicationException
     * if quantity is too high.
     *
     * @param Product $product
     * @param $quantity
     * @return bool
     * @throws ApplicationException
     */
    public function checkProductAvailability(Product $product, $quantity)
    {
        if ( !$product->track_inventory )
            return true;

        $total_quantity = $quantity;

        foreach ( $this->getItems() as $item )
            if ( $item->product->id == $product->id )
                $total_quantity += $item->qty;

        if ($total_quantity > $product->units_in_stock)
        {
            if ($product->units_in_stock > 0)
                throw new ApplicationException('We are sorry, but only '.$product->units_in_stock.' unit(s) of the "'.$product->title.'" product are available in stock.');
            else
                throw new ApplicationException('We are sorry, but the product "'.$product->title.'" is out of stock.');
        }

        return true;
    }

    /**
     * Creates or Updates a cartitem and returns its key.
     *
     * @param string     $key
     * @param Product    $product
     * @param int        $quantity
     * @param array      $details
     * @return CartItem  $item
     * @throws ApplicationException
     */
    protected function createCartItem($key, Product $product, $quantity, array $details)
    {
        if ( $this->hasItem($key) )
            throw new ApplicationException("Cart Item with this key already exists.");

        $this->items[$key] = new CartItem($product, intval($quantity), [
            'price' => doubleval($details['price']),
            'options' => $details['options'],
        ]);

        return $this->items[$key];
    }

    /**
     * Returns all cart items matching options the given product/option set.
     *
     * @param Product $product
     * @param array $options
     * @return array     Subset of $this->items with keys intact
     */
    protected function findMatchingItems(Product $product, array $options = [])
    {
        return array_where($this->getItems(), function($key, $item) use ($product, $options) {
            return $item->matches($product, $options);
        });
    }

    /**
     * Generates a unique key for a new Cart Item.
     *
     * @return string
     */
    protected function generateItemKey()
    {
        // Find unique key
        $key = time().'-';
        $i = 1;
        while ( $this->hasItem($key.$i) )
            $i++;
        // Unique key found
        $key = $key.$i;

        return $key;
    }

}
