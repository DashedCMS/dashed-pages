<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dashed__pages', 'order')) {
            Schema::table('dashed__pages', function (Blueprint $table) {
                $table->integer('order')->default(1)->after('public');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dashed__pages', 'order')) {
            Schema::table('dashed__pages', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }
};
