<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeKodecNullableInPelanggansTable extends Migration
{
    public function up()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->string('KODEC_toko')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->string('KODEC_toko')->nullable(false)->change();
        });
    }
}
