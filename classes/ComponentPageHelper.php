<?php namespace MelonCart\Shop\Classes;

use URL;
use Cms\Classes\Page;

class ComponentPageHelper
{
    use \October\Rain\Support\Traits\Singleton;

    protected $cachedPages = [];

    public function cart($params = [], $defaultUrl = '/cart')
    {
        return $this->getLastPageWithComponent('melonCart', $params, $defaultUrl);
    }

    public function category($params = [], $defaultUrl = '/category')
    {
        return $this->getLastPageWithComponent('melonCategory', $params, $defaultUrl);
    }

    public function checkout($params = [], $defaultUrl = '/checkout')
    {
        return $this->getLastPageWithComponent('melonCheckout', $params, $defaultUrl);
    }

    public function complete($params = [], $defaultUrl = '/complete')
    {
        return $this->getLastPageWithComponent('melonComplete', $params, $defaultUrl);
    }

    public function pay($params = [], $defaultUrl = '/pay')
    {
        return $this->getLastPageWithComponent('melonPay', $params, $defaultUrl);
    }

    public function product($params = [], $defaultUrl = '/product')
    {
        return $this->getLastPageWithComponent('melonProduct', $params, $defaultUrl);
    }

    public function manufacturer($params = [], $defaultUrl = '/manufacturer')
    {
        return $this->getLastPageWithComponent('melonManufacturer', $params, $defaultUrl);
    }



    protected function getLastPageWithComponent($componentName, $params = [], $defaultUrl = '/')
    {
        if ( !isset($this->cachedPages[$componentName]) )
        {
            $pages = Page::withComponent($componentName)->all();

            $this->cachedPages[$componentName] = end($pages);
        }

        // Page with given component found, return its URL
        if ( ($page = $this->cachedPages[$componentName]) )
            return Page::url($page->baseFileName, $params);
        // Page with given component not found, return the default URL
        else
            return URL::to($defaultUrl);
    }
}