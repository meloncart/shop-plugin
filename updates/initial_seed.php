<?php namespace MelonCart\Shop\Updates;

use Faker\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MelonCart\Shop\Models\Order;
use MelonCart\Shop\Models\OrderItem;
use MelonCart\Shop\Models\ShippingMethod;
use RainLab\User\Models\User;
use MelonCart\Shop\Models\Customer;
use MelonCart\Shop\Models\ProductType;
use MelonCart\Shop\Models\Manufacturer;
use MelonCart\Shop\Models\OrderStatus;
use MelonCart\Shop\Models\OrderStatusTransition;
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
        $faker = Factory::create();

        // Customer
        $user = User::first();
        $australia = Country::where('name', '=', 'Australia')->first();
        $queensland = State::where('name', '=', 'Queensland')->first();
        $customer = Customer::create([
            'user_id' => $user ? $user->id : 0,
            'shipping_name' => $user ? $user->name : $faker->firstName,
            'shipping_surname' => $user ? $user->surname : $faker->lastName,
            'shipping_company' => $faker->company,
            'shipping_phone' => $faker->phoneNumber,
            'shipping_country_id' => $australia->id,
            'shipping_state_id' => $queensland->id,
            'shipping_street_addr' => $faker->streetAddress,
            'shipping_city' => $faker->city,
            'shipping_zip' => $faker->postcode,
        ]);

        // Product types
        $productTypeGoods = ProductType::create([
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
        $manufacturer = Manufacturer::create([
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
        $taxClass = TaxClass::create([
            'title' => 'Default',
            'api_code' => 'default',
            // 'rates' => '[{"countryCode":"3","stateCode":"*","rate":"10%","title":"GST"}]',
            'is_default' => true,
        ]);

        // Shipping Methods
        $shippingMethod = ShippingMethod::create([
            'enabled_on_frontend' => false,
            'enabled_on_backend' => true,
            'type' => 'MelonCart\Shop\ShippingTypes\TableRate',
            'title' => 'Default',
            'config_data' => [
                'rates' => [],
            ],
            'is_taxable' => true,
            'handling_fee' => 6.5
        ]);


        // Order Status'
        $order_new = OrderStatus::create([
            'title' => 'New',
            'color' => '#0099cc',
            'notify_customer' => true,
            'customer_message_template' => 'meloncart:order_thankyou',
            'notify_recipients' => true,
            'system_message_template' => 'meloncart:order_new_internal',
        ]);

        $order_paid = OrderStatus::create([
            'title' => 'Paid',
            'color' => '#9acd32',
            'notify_customer' => false,
            'update_stock' => true,
            'system_message_template' => 'shop:order_paid_internal',
        ]);

        $order_shipped = OrderStatus::create([
            'title' => 'Shipped',
            'color' => '#04d215',
        ]);

        OrderStatusTransition::create([
            'from_status' => $order_new,
            'to_status' => $order_paid,
        ]);

        OrderStatusTransition::create([
            'from_status' => $order_paid,
            'to_status' => $order_shipped,
        ]);

        $products = [];
        for ( $i = 1; $i <= 30; $i++ )
        {
            $product = $products[] = Product::create([
                'is_enabled' => true,
                'slug' => 'my-product-'.$i,
                'title' => 'My Product ' . $i,
                'short_desc' => $faker->text(50),
                'description' => "<p>".$faker->text(200)."</p>",
                'manufacturer' => $manufacturer,
                'tax_class' => $taxClass,
                'product_type' => $productTypeGoods, // Goods
                //'default_om_id' => 1,
                'cost' => $faker->numberBetween(5000, 10000) / 100,
                'base_price' => $faker->numberBetween(2000, 4999) / 100,
                'sku' => 'my-sku',
                'units_in_stock' => $faker->numberBetween(0, 15),
                'hide_if_out_of_stock' => $faker->numberBetween(0, 1),
            ]);


            $product->product_options()->save(new ProductOption([
                'title' => "Colour",
                "values" => $faker->colorName . "\n" . $faker->colorName . "\n" . $faker->colorName,
            ]));

            $product->product_extras()->save(new ProductExtra([
                'title' => "Gift Wrapping",
                "price" => 10,
            ]));

            $product->categories()->attach($category);
            $product->save();
        }

        for ( $i = 0; $i < 5; $i++ )
        {
            $order_products = Arr::random($products, $faker->numberBetween(1, 4));
            $subtotal = array_reduce($order_products, function($carry, $product) {
                return $carry + $product->cost;
            });
            $shipping = 6.5;
            $total = $subtotal + $shipping;

            // @TODO Add payment and shipping methods
            $order = Order::create([
                'status' => $order_paid,
                'status_updated_at' => Carbon::now(),
                'customer' => $customer,
                'payment_method_id' => null,
                'shipping_method' => $shippingMethod,
                'customer_ip' => $faker->ipv4,
                'shipping_first_name' => $customer->shipping_name,
                'shipping_last_name' => $customer->shipping_surname,
                'shipping_company' => $customer->shipping_company,
                'shipping_phone' => $customer->shipping_phone,
                'shipping_country_id' => $customer->shipping_country_id,
                'shipping_state_id' => $customer->shipping_state_id,
                'shipping_street_addr' => $customer->shipping_street_addr,
                'shipping_city' => $customer->shipping_city,
                'shipping_zip' => $customer->shipping_zip,
                'billing_email' => $customer->user ? $customer->user->email : $faker->email,
                'billing_first_name' => $customer->shipping_name,
                'billing_last_name' => $customer->shipping_surname,
                'billing_company' => $customer->shipping_company,
                'billing_phone' => $customer->shipping_phone,
                'billing_country_id' => $customer->shipping_country_id,
                'billing_state_id' => $customer->shipping_state_id,
                'billing_street_addr' => $customer->shipping_street_addr,
                'billing_city' => $customer->shipping_city,
                'billing_zip' => $customer->shipping_zip,
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => 0,
                'shipping' => $shipping,
                'shipping_tax' => 0,
                'total' => $total,
                'payment_processed_at' => Carbon::now(),
            ]);

            foreach ( $order_products as $product )
            {
                $order->items()->save(new OrderItem([
                    'product' => $product,
                    'price' => $product->cost,
                    'quantity' => 1,
                ]));
            }
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
