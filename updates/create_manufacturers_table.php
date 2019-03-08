<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateManufacturersTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_manufacturers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('is_enabled')->default(1);
            $table->string('slug')->default('');
            $table->string('title')->default('');
            $table->text('description')->default('');
            $table->string('street_addr')->default('');
            $table->string('city')->default('');
            $table->string('zip')->default('');
            $table->string('phone')->default('');
            $table->string('fax')->default('');
            $table->string('email')->default('');
            $table->string('url')->default('');
            $table->integer('country_id')->unsigned()->default(0)->index();
            $table->integer('state_id')->unsigned()->default(0)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_manufacturers');
    }

}
