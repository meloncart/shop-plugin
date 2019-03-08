<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductOptionsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_product_options', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->unsigned()->index();
            $table->string('title')->default('');
            $table->text('values')->nullable();
            $table->integer('sort_order')->default(0)->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_product_options');
    }

}
