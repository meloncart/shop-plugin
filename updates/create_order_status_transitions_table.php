<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOrderStatusTransitionsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_order_status_transitions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('from_status_id')->nullable();
            $table->integer('to_status_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->unique(['from_status_id', 'to_status_id'], 'from_to_status_id_idx');
            $table->index('role_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_order_status_transitions');
    }

}
