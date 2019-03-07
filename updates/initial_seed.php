<?php namespace MelonCart\Shop\Updates;

use RainLab\User\Models\User;
use MelonCart\Shop\Models\Customer;
use MelonCart\Shop\Models\ProductType;
use MelonCart\Shop\Models\Manufacturer;
use MelonCart\Shop\Models\OrderStatus;
use MelonCart\Shop\Models\Category;
use MelonCart\Shop\Models\TaxClass;
use MelonCart\Shop\Models\Product;
use MelonCart\Shop\Models\ProductOption;
use MelonCart\Shop\Models\ProductExtra;
//use MelonCart\Shop\Models\ProductOMOption;
//use MelonCart\Shop\Models\ProductOMRecord;
//use MelonCart\Shop\Models\ProductOMRecordOption;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use October\Rain\Database\Updates\Seeder;

class InitialSeed extends Seeder
{

    public function run()
    {
        // Customer
        $user = User::first();
        $australia = Country::where('name', '=', 'Australia')->first();
        $queensland = State::where('name', '=', 'Queensland')->first();
        $customer = Customer::create([
            'user_id' => $user ? $user->id : 0,
            'shipping_name' => 'First',
            'shipping_surname' => 'Last',
            'shipping_company' => 'company',
            'shipping_phone' => '12345678',
            'shipping_country_id' => $australia->id,
            'shipping_state_id' => $queensland->id,
            'shipping_street_addr' => '123 fake street',
            'shipping_city' => 'city',
            'shipping_zip' => '1234',
        ]);

        // Product types
        ProductType::create([
            'title' => 'Goods',
            'api_code' => 'goods',
        ]);

        ProductType::create([
            'title' => 'Service',
            'api_code' => 'service',
        ]);

        ProductType::create([
            'title' => 'Downloadable',
            'api_code' => 'downloadable',
        ]);


        // Manufacturer
        Manufacturer::create([
            'is_enabled' => true,
            'title' => 'Example Manufacturer',
            'slug' => 'example-manufacturer',
            'country_id' => $australia->id,
            'state_id' => $queensland->id,
            'email' => 'example-manufacturer@dev.null',
            'description' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut semper a nisi sed ultrices. Sed et ultrices mauris. Suspendisse quis viverra quam, a elementum augue. Ut neque diam, interdum eget sollicitudin id, pharetra eu justo. Vivamus eros justo, congue eget lacus eget, ullamcorper eleifend metus. Praesent maximus, nibh id tincidunt tincidunt, mauris nisi ultricies ipsum, scelerisque mollis urna arcu sed eros. Ut tincidunt justo a pharetra ultricies. Morbi convallis lorem sit amet lectus molestie, vel consequat diam bibendum. Sed lacinia nisi ac sem laoreet, sit amet scelerisque sapien accumsan. Donec in velit neque. Pellentesque condimentum tellus non felis pretium, sit amet vulputate leo semper. Praesent odio ex, aliquam ac tincidunt eu, pharetra vel tellus. Nulla ac sem vitae augue tempor sollicitudin non sit amet diam. Quisque sed sapien vel purus venenatis porta vel eu massa. Integer malesuada ultrices neque quis vestibulum. Cras pretium, mi vel pretium condimentum, ante tellus vehicula turpis, ut dictum eros nunc ut urna.</p>",
        ]);

        // Product Category
        $category = Category::create([
            'title' => 'Example Category',
            'slug' => 'example-category',
            'description' => '<p>Just an example category</p>',
            'nest_left' => 1,
            'nest_right' => 2,
        ]);

        // Tax Classes
        TaxClass::create([
            'title' => 'Default',
            'api_code' => 'default',
            // 'rates' => '[{"countryCode":"3","stateCode":"*","rate":"10%","title":"GST"}]',
            'is_default' => true,
        ]);


        // Order Status'
        OrderStatus::create([
            'is_enabled' => true,
            'title' => 'New',
            'color' => '#0099cc',
            'api_code' => 'new',
            'notify_customer' => false,
        ]);

        OrderStatus::create([
            'is_enabled' => true,
            'title' => 'Paid',
            'color' => '#9acd32',
            'api_code' => 'paid',
            'notify_customer' => false,
        ]);

        OrderStatus::create([
            'is_enabled' => true,
            'title' => 'Shipped',
            'color' => '#04d215',
            'api_code' => 'shipped',
            'notify_customer' => true,
        ]);

        for ( $i = 1; $i <= 30; $i++ )
        {
            $product = Product::create([
                'is_enabled' => true,
                'slug' => 'my-product-'.$i,
                'title' => 'My Product ' . $i,
                'short_desc' => "Short description goes here.\n\n And some more here.",
                'description' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut semper a nisi sed ultrices. Sed et ultrices mauris. Suspendisse quis viverra quam, a elementum augue. Ut neque diam, interdum eget sollicitudin id, pharetra eu justo. Vivamus eros justo, congue eget lacus eget, ullamcorper eleifend metus. Praesent maximus, nibh id tincidunt tincidunt, mauris nisi ultricies ipsum, scelerisque mollis urna arcu sed eros. Ut tincidunt justo a pharetra ultricies. Morbi convallis lorem sit amet lectus molestie, vel consequat diam bibendum. Sed lacinia nisi ac sem laoreet, sit amet scelerisque sapien accumsan. Donec in velit neque. Pellentesque condimentum tellus non felis pretium, sit amet vulputate leo semper. Praesent odio ex, aliquam ac tincidunt eu, pharetra vel tellus. Nulla ac sem vitae augue tempor sollicitudin non sit amet diam. Quisque sed sapien vel purus venenatis porta vel eu massa. Integer malesuada ultrices neque quis vestibulum. Cras pretium, mi vel pretium condimentum, ante tellus vehicula turpis, ut dictum eros nunc ut urna.</p><p>Integer non libero ut lectus accumsan lobortis non a dolor. Suspendisse potenti. Sed et nisl nisl. Nam consequat lacinia ex, sit amet aliquam ex facilisis vel. In hac habitasse platea dictumst. Mauris commodo hendrerit interdum. Suspendisse tincidunt sodales justo vel cursus. Curabitur a sollicitudin mi.</p><p>Mauris fringilla tortor libero, nec venenatis ligula egestas a. Etiam lobortis, ante quis hendrerit pellentesque, enim quam dictum nulla, faucibus sollicitudin tellus lectus quis lectus. Maecenas quis magna non eros aliquam laoreet a quis dui. Curabitur venenatis cursus imperdiet. Nulla et venenatis metus. Nunc congue, diam eget rutrum congue, lectus nibh mattis mauris, sed eleifend augue ipsum id risus. Integer dapibus aliquet lorem in dictum. Integer at diam non odio rhoncus elementum vel nec ante.</p><p>Sed dignissim, ante et ultrices tincidunt, nisi arcu rutrum urna, non elementum turpis turpis et nibh. In malesuada ante in mattis vulputate. Pellentesque at neque sit amet felis aliquam posuere dapibus id lacus. Integer tristique turpis nunc, vel dictum tellus imperdiet non. In vulputate elit mattis faucibus rutrum. Nulla ut ultricies turpis, ac sodales velit. Nullam in odio at erat blandit suscipit eget vitae orci. Nulla tincidunt, felis sit amet eleifend elementum, justo lectus venenatis massa, et iaculis dui mauris at diam. Aenean feugiat mollis nulla, et aliquam lectus cursus sed. Donec vestibulum purus posuere, condimentum ante vitae, gravida orci.</p>",
                'manufacturer_id' => 1,
                'tax_class_id' => 1,
                'product_type_id' => 1, // Goods
                //'default_om_id' => 1,
                'cost' => 5.39,
                'base_price' => 5.80,
                'sku' => 'my-sku',
                'hide_if_out_of_stock' => 1,
            ]);


            $product->product_options()->save(new ProductOption([
                'title' => "Colour",
                "values" => "Red\nGreen\nBlue",
            ]));

            $product->product_extras()->save(new ProductExtra([
                'title' => "Gift Wrapping",
                "price" => 10,
            ]));

            $product->categories()->attach($category);
            $product->save();
        }

        // Default OM Record
        //ProductOMRecord::find(1)->update([
        //    'cost' => 5.39,
        //    'price' => 5.80,
        //    'sku' => 'my-sku',
        //    'hide_if_out_of_stock' => 1,
        //]);

        // Create 30 options
        // for ( $i=2; $i<32; $i++ )
        //     ProductOMRecord::create([
        //         'product_id' => 1,
        //         'cost' => 5.39 + $i,
        //         'price' => 5.80 + $i,
        //         'sku' => 'my-sku-' . $i,
        //         'hide_if_out_of_stock' => 1,
        //     ]);

        // A custom option field
        //ProductOMOption::create([
        //    'product_id' => 1,
        //    'title' => 'Size',
        //    'code' => 'size',
        //    'values' => "S\nM",
        //]);
        //ProductOMOption::create([
        //    'product_id' => 1,
        //    'title' => 'Color',
        //    'code' => 'color',
        //    'values' => "Black\nWhite",
        //]);
        //ProductOMOption::create([
        //    'product_id' => 1,
        //    'title' => 'Weight',
        //    'code' => 'size',
        //    'values' => "Light\nHeavy",
        //]);

        // Associate records and options
        // $arr = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        // for ( $i=2; $i<32; $i++ )
        //     for ( $j=0; $j<sizeof($arr); $j++ )
        //         ProductOMRecordOption::create([
        //             'om_record_id' => $i,
        //             'om_option_id' => 1,
        //             'value' => $arr[$j],
        //         ]);
    }

}
