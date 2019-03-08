<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductsCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_products_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['product_id', 'category_id'], 'prods_cats_prod_id_cat_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_products_categories');
    }

}
