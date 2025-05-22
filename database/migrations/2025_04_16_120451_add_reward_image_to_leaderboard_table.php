<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->string('reward_image')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('reward_image');
        });
    }
};
