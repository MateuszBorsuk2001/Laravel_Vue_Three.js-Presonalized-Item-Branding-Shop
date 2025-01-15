<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUnitPriceToItems extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('unit_price', 8, 2)->default(0.00)->after('model');
        });

        // Populate unit_price based on model
        DB::table('items')->where('model', 'mug')->update(['unit_price' => 1.00]);
        DB::table('items')->where('model', 'tshirt')->update(['unit_price' => 2.00]);
        DB::table('items')->where('model', 'cap')->update(['unit_price' => 3.00]);
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('unit_price');
        });
    }
}
