<?php namespace MelonCart\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductOMRecordOptionsTable extends Migration
{

    public function up()
    {
        Schema::create('mc_shop_product_om_record_options', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('om_record_id')->unsigned();
            $table->integer('om_option_id')->unsigned()->index(); // omrecordoptions_om_option_id
            $table->string('value');
            $table->primary(['om_record_id', 'value'], 'omrecordoptions_record_id_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mc_shop_product_om_record_options');
    }

}
