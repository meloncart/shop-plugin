<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateUserCartItemsTable extends Migration
{
    public function up()
    {
        Schema::create('mc_shop_user_cart_items', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_user_cart_items');
    }
}
