<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

/**
 * Store the cross-sell position as an integer.
 *
 * It was a string column, so ordering by it put "10" before "2". The Filament
 * reorder writes plain integers.
 */
return new class() extends Migration
{
    public function up(): void
    {
        Schema::table($this->prefix.'cross_sells', function (Blueprint $table) {
            $table->unsignedInteger('position')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table($this->prefix.'cross_sells', function (Blueprint $table) {
            $table->string('position')->nullable()->change();
        });
    }
};
