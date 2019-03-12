<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOrderItemTaxesTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_order_item_taxes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('order_item_id')->nullable()->default(0)->unsigned()->index();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2)->nullable()->default(0)->unsigned();
            $table->decimal('discount', 10, 2)->nullable()->default(0)->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_order_item_taxes');
    }

}
