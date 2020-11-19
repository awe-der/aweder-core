<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('categories', 'image')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->text('image')->nullable()->after('visible');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('categories', 'image')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
}
