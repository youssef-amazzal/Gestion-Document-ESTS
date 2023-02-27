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
        Schema::create('group_privilege', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                  ->constrained('groups')
                  ->onDelete('cascade');

            $table->foreignId('privilege_id')
                  ->constrained('privileges')
                  ->onDelete('cascade');

            $table->unique(['group_id', 'privilege_id']);

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
        Schema::dropIfExists('group_privilege');
    }
};
