<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dashed__pages', function (Blueprint $table) {
            $table->dropForeign('dashed__pages_parent_page_id_foreign');
            $table->renameColumn('parent_page_id', 'parent_id');
        });
        Schema::table('dashed__pages', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->change()
                ->nullable()
                ->constrained('dashed__pages')
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
