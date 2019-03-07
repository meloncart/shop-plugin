<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateShippingMethodCountriesTable extends Migration
{

    public function up()
    {
        Schema::create('meloncart_shop_shipping_method_countries', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('shipping_method_id')->default(0)->unsigned();
            $table->integer('country_id')->default(0)->unsigned();
            $table->primary(['shipping_method_id', 'country_id'], 'shipping_method_id_country_id_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('meloncart_shop_shipping_method_countries');
    }

}
