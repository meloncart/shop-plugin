<?php namespace Radiantweb\Flocommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTaxClassesTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_tax_classes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title')->default('')->unique();
            $table->text('description')->default('');
            $table->string('api_code')->default('')->unique();
            $table->boolean('is_default')->default(false);
            $table->text('rates')->default('');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_tax_classes');
    }

}
