<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductOMRecordsTable extends Migration
{

    public function up()
    {
        Schema::create('meloncart_shop_product_om_records', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->default(0)->index('prod_om_records_product_id');
            $table->decimal('cost', 15, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('sku')->nullable();
            $table->float('weight')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('depth')->nullable();
            $table->integer('units_in_stock')->nullable()->index('prod_om_records_units_in_stock');
            $table->boolean('track_inventory')->nullable()->index('prod_om_records_track_inventory');
            $table->boolean('hide_if_out_of_stock')->nullable()->index('prod_om_records_hide_if_out_of_stock');
            $table->integer('tax_class_id')->nullable()->unsigned()->index('prod_om_records_tax_class_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meloncart_shop_product_om_records');
    }

}
