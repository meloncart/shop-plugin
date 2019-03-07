<?php namespace MelonCart\Shop\Components;

use Redirect;
use Cms\Classes\ComponentBase;
use MelonCart\Shop\Models\Manufacturer as ManufacturerModel;
//use MelonCart\Shop\Models\Product;

class Manufacturer extends ComponentBase
{
    /**
     * A collection of posts to display
     * @var \Illuminate\Pagination\LengthAwarePaginator of \MelonCart\Shop\Models\Product
     */
    public $products;

    /**
     * If the post list should be filtered by a manufacturer, the model to use.
     * @var \MelonCart\Shop\Models\Manufacturer
     */
    public $manufacturer;

    /**
     * Whether or not to display full post content. If not, display excerpt instead.
     * @var bool
     */
    public $useFullContent;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noPostsMessage;

    /**
     * If the post list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'Manufacturer',
            'description' => 'Displays a list of Products in a given Manufacturer.'
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
            'productsPerPage' => [
                'title'             => 'Products per page',
                'type'              => 'string',
                'validationPattern' => '^[1-9][0-9]*$',
                'validationMessage' => 'Invalid format of the products per page value',
                'default'           => '10',
            ],
            'noProductsMessage' => [
                'title'        => 'No products message',
                'description'  => 'Message to display in place of the product list when there are no products.',
                'type'         => 'string',
                'default'      => 'No products found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'Products order',
                'description' => 'Attribute on which the products should be ordered',
                'type'        => 'dropdown',
                'default'     => 'title asc'
            ],
        ];
    }

    public function getSortOrderOptions()
    {
        $options = Product::$allowedSortingOptions;
        $ret = [];

        foreach ( $options as $option )
        {
            $ret["$option asc"] = ucfirst($option) . " (Ascending)";
            $ret["$option desc"] = ucfirst($option) . " (Descending)";
        }

        return $ret;
    }

    public function onRun()
    {
        $this->prepareVars();

        list($slug, $page) = $this->parseSlug($this->property('slug'));

        $this->manufacturer = $this->page['manufacturer'] = $this->loadManufacturer($slug);
        $this->products = $this->page['products'] = $this->manufacturer ?
            $this->listProducts($this->manufacturer, ['page' => $page]) :
            [];

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->page['products']->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->page['noPoroductsMessage'] = $this->property('noProductsMessage');
    }

    protected function listProducts(ManufacturerModel $manufacturer, array $options = [])
    {
        return $manufacturer->products()
            ->listFrontEnd([
                'page'       => empty($options['page']) ? 0 : $options['page'],
                'sort'       => $this->property('sortOrder'),
                'perPage'    => $this->property('postsPerPage'),
            ]);
    }

    /**
     * Return the Category with a given nested Slug
     *
     * @param $slug
     * @return BlogManufacturer|null
     */
    protected function loadManufacturer($slug)
    {
        return ManufacturerModel::whereSlug($slug)->first();
    }

    /**
     * Our page URL is /shop/category/:slug* - this means the optioanl /page/4
     * will be lumped in with :slug*. This method breaks the slug up appropriately
     * and returns the results.
     *
     * @param $slug    /foo/bar/baz[/page/4]
     * @return array   [:catSlug*, :pageNum = 0]
     */
    public function parseSlug($slug)
    {
        preg_match('/(.+?)(?=\/page\/(\d+)|$)/', $slug, $matches);

        // Page number wasn't explicitly
        if ( count($matches) < 3 )
            $matches[] = 0;
        else
            $matches[2] = intval($matches[2]); // cast the string to an int

        array_shift($matches);

        return $matches;
    }
}
