<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('is_enabled')->default(false)->index();
            $table->string('slug')->index();
            $table->string('title')->default('')->index();
            $table->text('short_desc')->default('');
            $table->text('description')->default('');
            $table->integer('manufacturer_id')->nullable()->default(0)->unsigned()->index();
            $table->integer('tax_class_id')->nullable()->default(0)->unsigned()->index();
            $table->integer('product_type_id')->nullable()->default(0)->unsigned()->index();
            //$table->integer('default_om_id')->default(0)->unsigned()->index();
            $table->decimal('cost', 15, 2)->nullable();
            $table->decimal('base_price', 15, 2)->nullable();
            $table->boolean('is_on_sale')->nullable()->default(false)->index(); // prod_is_on_sale
            $table->string('sale_price')->nullable();
            $table->string('sku')->nullable()->index(); // prod_sku
            $table->float('weight')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('depth')->nullable();
            $table->integer('units_in_stock')->nullable()->index(); // prod_units_in_stock
            $table->boolean('track_inventory')->nullable()->index(); // prod_track_inventory
            $table->boolean('hide_if_out_of_stock')->nullable()->index(); // prod_hide_if_oos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_products');
    }

}
