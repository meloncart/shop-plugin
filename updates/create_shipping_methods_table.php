<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateShippingMethodsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_shipping_methods', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('enabled_on_frontend')->nullable()->default(1)->index(); // enabled_on_frontend_idx
            $table->boolean('enabled_on_backend')->nullable()->default(1)->index(); // enabled_on_backend_idx
            $table->string('type')->default('');
            $table->string('title')->default('');
            $table->text('description')->nullable()->default('');
            $table->text('config_data')->nullable()->default('');
            $table->boolean('is_taxable')->nullable()->default(1);
            $table->decimal('handling_fee', 15, 2)->nullable()->default(0);
            $table->float('max_weight')->nullable()->default(0);
            $table->float('min_weight')->nullable()->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_shipping_methods');
    }

}
