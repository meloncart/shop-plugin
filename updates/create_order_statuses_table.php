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
            $table->boolean('is_enabled')->nullable()->default(false);
            $table->string('title')->default('');
            $table->string('color')->default('');
            $table->string('api_code')->default('')->unique();
            $table->boolean('notify_customer')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meloncart_shop_order_statuses');
    }

}
