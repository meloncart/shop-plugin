<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOrdersTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_orders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('customer_id')->nullable()->default(0)->unsigned()->index();
            $table->integer('payment_method_id')->nullable()->default(0)->unsigned()->index();
            $table->string('customer_ip')->nullable()->default('');
            $table->string('billing_first_name')->nullable()->default('');
            $table->string('billing_last_name')->nullable()->default('');
            $table->string('billing_email')->nullable()->default('');
            $table->string('billing_phone')->nullable()->default('');
            $table->string('billing_company')->nullable()->default('');
            $table->string('billing_street_addr')->nullable()->default('');
            $table->string('billing_city')->nullable()->default('');
            $table->string('billing_zip')->nullable()->default('');
            $table->integer('billing_country_id')->unsigned()->default(0)->index();
            $table->integer('billing_state_id')->unsigned()->default(0)->index();
            $table->string('shipping_first_name')->nullable()->default('');
            $table->string('shipping_last_name')->nullable()->default('');
            $table->string('shipping_email')->nullable()->default('');
            $table->string('shipping_phone')->nullable()->default('');
            $table->string('shipping_company')->nullable()->default('');
            $table->string('shipping_street_addr')->nullable()->default('');
            $table->string('shipping_city')->nullable()->default('');
            $table->string('shipping_zip')->nullable()->default('');
            $table->integer('shipping_country_id')->unsigned()->default(0)->index();
            $table->integer('shipping_state_id')->unsigned()->default(0)->index();
            $table->decimal('discount', 15, 2)->default(0)->unsigned();
            $table->decimal('tax', 15, 2)->default(0)->unsigned();
            $table->decimal('subtotal', 15, 2)->default(0)->unsigned();
            $table->decimal('shipping', 15, 2)->default(0)->unsigned();
            $table->decimal('shipping_tax', 15, 2)->default(0)->unsigned();
            $table->integer('shipping_method_id')->default(0)->unsigned()->index();
            $table->decimal('total', 15, 2)->default(0)->unsigned();
            $table->integer('status_id')->default(0)->unsigned()->index();
            $table->datetime('status_updated_at')->nullable();
            $table->datetime('payment_processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_orders');
    }

}
