<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductTypesTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_product_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title')->default('');
            $table->string('api_code')->default('')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_product_types');
    }

}
