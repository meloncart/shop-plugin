<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProductPropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('meloncart_shop_product_properties', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meloncart_shop_product_properties');
    }
}
