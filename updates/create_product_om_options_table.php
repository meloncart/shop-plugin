<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductOMOptionsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_product_om_options', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->default(0)->index('prod_om_options_product_id');
            $table->string('title')->default('');
            $table->string('code')->default('')->index('prod_om_options_code');
            $table->text('values')->default('');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_product_om_options');
    }

}
