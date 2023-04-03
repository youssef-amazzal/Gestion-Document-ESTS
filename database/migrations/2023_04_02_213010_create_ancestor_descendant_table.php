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
        Schema::create('containables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')
                ->constrained('folders')
                ->onDelete('cascade');
            $table->foreignId('containable_id');
            $table->string('containable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ancestor_descendant');
    }
};
