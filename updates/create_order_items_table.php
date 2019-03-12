<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOrderItemsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_order_items', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->nullable()->default(0)->unsigned()->index();
            $table->integer('order_id')->nullable()->default(0)->unsigned()->index();
            $table->decimal('price', 10, 2)->nullable()->default(0)->unsigned();
            $table->integer('quantity')->nullable()->default(0)->unsigned();
            $table->text('options')->nullable();
            $table->text('extras')->nullable();
            $table->decimal('extras_price', 10, 2)->nullable()->default(0)->unsigned();
            $table->decimal('discount', 10, 2)->nullable()->default(0)->unsigned();
            $table->decimal('tax', 10, 2)->nullable()->default(0)->unsigned();
            $table->decimal('discount_tax_included', 10, 2)->nullable()->default(0)->unsigned();
            $table->decimal('price_tax_included', 10, 2)->nullable()->default(0)->unsigned();
            $table->decimal('cost', 10, 2)->nullable()->default(0)->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_order_items');
    }

}
