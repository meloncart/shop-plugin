<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOrderStatusesTable extends Migration
{

    public function up()
    {
        Schema::create('meloncart_shop_order_statuses', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title')->default('');
            $table->string('color')->default('');
            $table->boolean('notify_customer')->default(0);
            $table->string('customer_message_template')->nullable();
            $table->boolean('notify_recipients')->default(0);
            $table->boolean('update_stock')->default(0);
            $table->string('system_message_template')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meloncart_shop_order_statuses');
    }

}
