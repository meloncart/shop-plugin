<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePriceRuleConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('mc_shop_price_rule_conditions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('rule_host_type', 100)->nullable();
            $table->string('condition_control_type', 100)->nullable();
            $table->string('class_name')->nullable();
            $table->mediumText('config_data')->nullable();
            $table->integer('rule_host_id')->unsigned()->nullable()->index();
            $table->integer('rule_parent_id')->unsigned()->nullable()->index();
            $table->index(['rule_host_id', 'rule_host_type'], 'idx_host_rule_id_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_price_rule_conditions');
    }
}
