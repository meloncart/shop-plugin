<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('meloncart_shop_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('slug')->index();
            $table->string('title')->default('');
            $table->text('short_desc')->default('');
            $table->text('description')->default('');

            // Nesting
            $table->integer('parent_id')->nullable()->unsigned()->index();
            $table->integer('nest_left')->nullable()->index();
            $table->integer('nest_right')->nullable()->index();
            $table->integer('nest_depth')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meloncart_shop_categories');
    }

}
