<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCatalogPriceRulesTable extends Migration
{
    public function up()
    {
        Schema::create('mc_shop_catalog_rules', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_terminating')->default(false);
            $table->string('action_class_name')->nullable();
            $table->mediumText('action_data')->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });

        Schema::create('mc_shop_catalog_rules_user_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('catalog_rule_id')->unsigned();
            $table->integer('user_group_id')->unsigned();
            $table->primary(['catalog_rule_id', 'user_group_id'], 'catalog_rule_user_group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_catalog_rules');
        Schema::dropIfExists('mc_shop_catalog_rules_user_groups');
    }
}
