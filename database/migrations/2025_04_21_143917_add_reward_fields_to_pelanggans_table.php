<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->bigInteger('target1')->nullable();
            $table->bigInteger('target2')->nullable();
            $table->text('deskripsi_hadiah_target1')->nullable(); 
            $table->text('deskripsi_hadiah_target2')->nullable(); 
            $table->text('deskripsi_hadiah')->nullable();
            $table->string('gambar_hadiah')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn(['target1', 'target2', 'deskripsi_hadiah', 'gambar_hadiah']);
        });
    }
};
