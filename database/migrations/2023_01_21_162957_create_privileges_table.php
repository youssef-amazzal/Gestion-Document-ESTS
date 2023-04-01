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
        Schema::create('privileges', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['file', 'system']);
            $table->string('action');
            $table->foreignId('grantor_id')
                ->nullable() //nullable because there will be an admin user that will be created by the system
                ->constrained('users')
                ->onDelete('cascade');
            // the privilege is granted on the target (file or folder)
            $table->foreignId('target_id')->nullable();
            $table->string('target_type')->nullable();
            // can be either a user or a group
            $table->foreignId('grantee_id');
            $table->string('grantee_type');

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
        Schema::dropIfExists('privileges');
    }
};
