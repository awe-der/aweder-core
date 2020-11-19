<?php

use Illuminate\Database\Migrations\Migration;

class MerchantIDonCategoryCanBeNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE categories MODIFY merchant_id bigint unsigned null;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE categories MODIFY merchant_id bigint unsigned not null;');
    }
}
