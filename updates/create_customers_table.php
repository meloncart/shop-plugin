<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCustomersTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_customers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->nullable()->index();
            $table->string('shipping_name')->nullable();
            $table->string('shipping_surname')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->integer('shipping_country_id')->unsigned()->nullable()->index();
            $table->integer('shipping_state_id')->unsigned()->nullable()->index();
            $table->string('shipping_street_addr')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->boolean('shipping_addr_is_business')->nullable()->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_customers');
    }
}
