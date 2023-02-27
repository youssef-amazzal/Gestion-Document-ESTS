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
            $table->enum('name', [
                // File privileges
                'create',
                'read',
                'update',
                'delete',
                'share',
                'download',
                'upload',
                // System privileges
                'grant',
                'revoke',
                'create_group',
                'delete_group',
                'add_user_to_group',
                'remove_user_from_group',
                'create_user',
                'edit_user',
                'delete_user',
                'backup',
                'restore'
            ]);
            $table->foreignId('granted_on')
                  ->nullable()
                  ->constrained('files')
                  ->onDelete('cascade');

            $table->foreignId('granted_by')
                  ->nullable() //nullable because there will be an admin user that will be created by the system
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('granted_to')
                  ->constrained('users')
                  ->onDelete('cascade');

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
