<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qcommerce__pages', function (Blueprint $table) {
            $table->dropForeign('qcommerce__pages_parent_page_id_foreign');
            $table->renameColumn('parent_page_id', 'parent_id');
            $table->foreignId('parent_id')
                ->change()
                ->nullable()
                ->constrained('qcommerce__pages')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('array', function (Blueprint $table) {
            //
        });
    }
};
