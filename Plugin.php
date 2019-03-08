<?php namespace MelonCart\Shop;

use Backend;
use Event;
use Backend\Classes\NavigationManager;
use System\Classes\PluginBase;

/**
 * MelonCart Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = [
        'RainLab.User',
        'RainLab.Location',
        'RainLab.UserPlus',
        'RainLab.Notify',
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'MelonCart',
            'description' => 'A comprehensive and flexible shopping cart plugin for October CMS',
            'author'      => 'MelonCart',
            'icon'        => 'icon-shopping-cart'
        ];
    }

    public function registerNavigation()
    {
        return [
            'reports' => [
                'label'       => 'Reports',
                'url'         => Backend::url('meloncart/shop/orders'),
                'icon'        => 'icon-leaf',
                'iconSvg'     => 'plugins/meloncart/shop/assets/images/report-icon.svg',
                'permissions' => ['meloncart.shop.*'],
                'order'       => 150,
            ],
            'shop' => [
                'label'       => 'Shop',
                'url'         => Backend::url('meloncart/shop/orders'),
                'icon'        => 'icon-shopping-cart',
                'iconSvg'     => 'plugins/meloncart/shop/assets/images/shop-icon.svg',
                'permissions' => ['meloncart.shop.*'],
                'order'       => 500,

                'sideMenu' => [
                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('meloncart/shop/categories'),
                        'permissions' => ['meloncart.shop.manage_categories'],
                    ],
                    'products' => [
                        'label'       => 'Products',
                        'icon'        => 'icon-gift',
                        'url'         => Backend::url('meloncart/shop/products'),
                        'permissions' => ['meloncart.shop.manage_products'],
                    ],
                    'orders' => [
                        'label'       => 'Orders',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('meloncart/shop/orders'),
                        'permissions' => ['meloncart.shop.manage_orders_customers'],
                    ],
                    'customers' => [
                        'label'       => 'Customers',
                        'icon'        => 'icon-users',
                        'url'         => Backend::url('meloncart/shop/customers'),
                        'permissions' => ['meloncart.shop.manage_orders_customers'],
                    ],
                    'taxclasses' => [
                        'label'       => 'Tax Classes',
                        'icon'        => 'icon-table',
                        'url'         => Backend::url('meloncart/shop/taxclasses'),
                        'permissions' => ['meloncart.shop.manage_orders_customers'],
                    ],
                    'shippingmethods' => [
                        'label'       => 'Shipping Methods',
                        'icon'        => 'icon-truck',
                        'url'         => Backend::url('meloncart/shop/shippingmethods'),
                        'permissions' => ['meloncart.shop.manage_orders_customers'],
                    ],
                    'rules' => [
                        'label'       => 'Price Rules',
                        'icon'        => 'icon-list-alt',
                        'url'         => Backend::url('meloncart/shop/catalogrules'),
                    ],
                    'discounts' => [
                        'label'       => 'Discounts',
                        'icon'        => 'icon-shopping-basket',
                        'url'         => Backend::url('meloncart/shop/cartrules'),
                    ],
                ]

            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'shipping' => [
                'label'       => 'Shipping',
                'description' => 'Specify a shipping origin and default location, weight and dimension units.',
                'icon'        => 'icon-truck',
                'class'       => 'MelonCart\Shop\Models\ShippingSettings',
                'category'    => 'Shop',
                'order'       => 600,
            ],
        ];
    }

    public function registerComponents()
    {
        return [
            'MelonCart\Shop\Components\Cart'           => 'melonCart',
            'MelonCart\Shop\Components\Category'       => 'melonCategory',
            'MelonCart\Shop\Components\Checkout'       => 'melonCheckout',
            'MelonCart\Shop\Components\Product'        => 'melonProduct',
            'MelonCart\Shop\Components\Manufacturer'   => 'melonManufacturer',
        ];
    }

    public function registerPermissions()
    {
        return [
            'meloncart.shop.manage_categories'          => ['label' => 'Manage Categories', 'tab' => 'MelonCart'],
            'meloncart.shop.manage_products'  => ['label' => 'Manage Products', 'tab' => 'MelonCart'],
            'meloncart.shop.manage_config'  => ['label' => 'Manage Shop Configuration', 'tab' => 'MelonCart'],
            'meloncart.shop.manage_orders_customers'  => ['label' => 'Manage Orders & Customers', 'tab' => 'MelonCart'],
        ];
    }


    public function register_meloncart_shipping_types()
    {
        return [
            'MelonCart\\Shop\\ShippingTypes\\TableRate' => [
                'label' => 'Table Rate',
                'description' => 'Allows configuration of shipping quotes based on location. No shipping service service accounts are required for this type.'
            ],
        ];
    }
}
