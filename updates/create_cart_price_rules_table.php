<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCartPriceRulesTable extends Migration
{
    public function up()
    {
        Schema::create('mc_shop_cart_rules', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_terminating')->default(false);
            $table->boolean('is_free_shipping')->default(false);
            $table->integer('sort_order')->nullable();
            $table->string('action_class_name')->nullable();
            $table->mediumText('action_data')->nullable();
            $table->integer('coupon_id')->unsigned()->nullable()->index();
            $table->integer('max_coupon_uses')->nullable();
            $table->integer('max_customer_uses')->nullable();
            $table->timestamps();
        });

        Schema::create('mc_shop_cart_rules_user_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('cart_rule_id')->unsigned();
            $table->integer('user_group_id')->unsigned();
            $table->primary(['cart_rule_id', 'user_group_id'], 'cart_rule_user_group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_cart_rules');
        Schema::dropIfExists('mc_shop_cart_rules_user_groups');
    }
}
