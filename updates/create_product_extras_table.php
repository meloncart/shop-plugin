<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductExtrasTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_product_extras', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->unsigned()->index();
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->float('weight')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('depth')->nullable();
            $table->integer('sort_order')->default(0)->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_product_extras');
    }

}
